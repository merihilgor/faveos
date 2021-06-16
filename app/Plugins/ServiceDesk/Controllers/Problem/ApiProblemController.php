<?php

namespace App\Plugins\ServiceDesk\Controllers\Problem;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Model\helpdesk\Ticket\Tickets;
use Config;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Request\Problem\ProblemRequest;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Illuminate\Http\Request;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketType;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use File;
use App\User;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use Validator;
use App\Http\Controllers\Agent\helpdesk\TicketController;

/**
 * Handles API's for Problem Controller
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiProblemController extends BaseServiceDeskController
{
	// managing agent permission
    protected $agentPermission;

    public function __construct() {
        $this->middleware('role.agent');
        $this->agentPermission = new AgentPermissionPolicy();
    }

	public function appendProblemToTimelineData(array &$ticket)
	{
	    $ticketId = $ticket['id'];

	    $problemId = CommonTicketRelation::where('type','sd_problem')
	        ->where('ticket_id', $ticketId)->value('type_id');

	    $associatedProblem = SdProblem::where('id',$problemId)
	        ->select('id','subject as name','description')->first();

	    if($associatedProblem){
	      $associatedProblem->description = strip_tags($associatedProblem->description);

	      $associatedProblem->redirect_to = Config::get('app.url')."/service-desk/problem/$associatedProblem->id/show";
	    }

	    $ticket['associated_problem'] = $associatedProblem;
	}

	/**
	* Function to create and update Problem
	* @param $request
	* @return Response
	*/
	public function createUpdateProblem(ProblemRequest $request)
	{
		$problem = $request->toArray();
		$problemObject = SdProblem::updateOrCreate(['id' => $request->id], $problem);
		$assetIds = [];
          foreach ((array) $request->asset_ids as $assetId) {
            $assetIds[$assetId] = ['type' => 'sd_problem'];
          }
          $problemObject->attachAssets()->sync($assetIds);
 
		$this->fillAttachment($request, $problemObject);

		if ($request->has('ticket_id')) {
			$problemObject->attachTickets()->attach([$problem['ticket_id'] => ['type' => 'sd_problem']]);
		}
		return successResponse(trans('ServiceDesk::lang.problem_saved_successfully'));
	}

	/**
	* Function to fill attachment
	* @param file $request
	* @return 
	*/
	private function fillAttachment($request, $problem)
	{
		
		$attachment = Attachments::where('owner', 'sd_problem:' . $problem->id)->first();
        if($this->shallDeleteAttachment($request, $attachment)) {
            $file = $attachment->value;
            File::delete('uploads/service-desk/attachments/' . $file);
            Attachments::where('owner', 'sd_problem:' . $problem->id)->delete();
            ($request->attachment_delete) ? $problem->manuallyLogActivityForPivot($problem, 'attachment','', 'deleted', null,'problem_attachment', 0, null) : '';
        }
        if ($request->file('attachment')) {
            $attachments[] = $request->file('attachment');
            UtilityController::attachment($problem->id, 'sd_problem', $attachments);
            $problem->manuallyLogActivityForPivot($problem, 'attachment', $request->file('attachment'), 'added', null, 'problem_attachment', 0, null);
        }
	}

	/**
	* Function to delete attachment
	* @param file $request
	* @param file $attachment
	* @return boolean
	*/
	private function shallDeleteAttachment($request, $attachment)
	{
		if($request->attachment_delete)
		{
			return true;
		}
		if($request->file('attachment') && $attachment)
		{
			return true;
		}
	}

	/**
	* Function to edit problem
	* @param SdProblem $problem
	* @return $error
	*/
	public function editProblem(SdProblem $problem)
	{
		$problem = $problem->where('id', $problem->id)->with([
			'department:id,name',
			'impact:id,name',
			'status:id,name',
			'location:id,title',
			'priority:priority_id,priority',
			'assignedTo:id,first_name,last_name,email,profile_pic',
			'attachAssets',
			'attachChange' => function ($query) {
                $query->with(['requester','status','priority','changeType','impactType','location','team','department'
                ]);
            },
		])
		->first()->toArray();
		$problem = $this->formatProblem($problem);

		return successResponse('', $problem);

	}

	/**
	* Function to format edit problem
	* @param array $problem
	* @return array $problem
	*/
	private function formatProblem($problem)
	{
		$fromQuery = User::where('id', $problem['requester_id'])->first();
		$problem['requester_id'] = ['id' => $fromQuery->id, 'name' => $fromQuery->first_name .' '. $fromQuery->last_name, 'profile_pic' => $fromQuery->profile_pic];
		$problem['status_type_id'] = $problem['status'];
		$problem['priority_id'] = ['id' => $problem['priority']['priority_id'], 'name' => $problem['priority']['priority']];
		$problem['impact_id'] = $problem['impact'];
		if ($problem['location']['id']) {
			$problem['location_id'] = ['id' => $problem['location']['id'], 'name' => $problem['location']['title']]; 
		}
		else {
			$problem['location_type_id'] ="";
		}
		if ($problem['assigned_to']['id']) {
			$problem['assigned_id'] = ['id' => $problem['assigned_to']['id'], 'name' => $problem['assigned_to']['first_name'] .' '. $problem['assigned_to']['last_name'], 'profile_pic' => $problem['assigned_to']['profile_pic']];
		}
		else {
			$problem['assigned_id'] ="";
		}
		$attach_assets = [];
		foreach ($problem['attach_assets'] as $assetIds) {
			$attach_assets[] = ['id' => $assetIds['id'], 'name' => SdAssets::find($assetIds['id'])->name];
		}
		$problem['assets'] = $attach_assets;
		$problem['department_id'] = $problem['department'];
		$problem['change'] = $problem['attach_change'];
		if($problem['attach_change'] != NULL)
		$problem['change']= $problem['attach_change'][0];
		$problem['attachment'] = Attachments::where('owner', 'sd_problem:' . $problem['id'])->first();
		unset($problem['attach_change'],$problem['impact'], $problem['status'], $problem['location'], $problem['priority'], $problem['assigned_to'], $problem['agent_id'], $problem['group_id'], $problem['attach_assets'], $problem['department'],$problem['change']['status_id'],$problem['change']['status_id'],$problem['change']['status_id'],$problem['change']['requester_id'],$problem['change']['priority_id'],$problem['change']['change_type_id'],$problem['change']['impact_id'],$problem['change']['location_id'],$problem['change']['approval_id'],$problem['change']['team_id'],$problem['change']['department_id']);

		return $problem;

	}

	/**
	* Function to get problem
	* @param $problemId
	* @return Response
	*/
	public function getProblem(SdProblem $problem)
	{
		$problem = $contract->where('id',$problem->id)->with([
			'department:id,name',
			'impact:id,name',
			'status:id,name',
			'location:id,title',
			'priority:priority_id,priority',
			'assignedTo:id,first_name,last_name,email'
		])
		->first()->toArray();

		$problem = $this->getFormatProblem($problem);


		return successResponse('', $problem);

	}

	/**
	* Function to format problem
	* @param array $problem
	* @return array $problem
	*/
	private function getFormatProblem($problem)
	{
		$problem['requester'] = ['id' => User::where('id', $problem['requester_id'])->first()->id, 'id' => $problem['requester_id'], 'name' => User::where('id' ,$problem['requester_id'])->first()->first_name .' '. User::where('id', $problem['requester_id'])->first()->last_name];
		$problem['priority'] = ['id' => $problem['priority']['priority_id'], 'name' => $problem['priority']['priority']];

		$problem['location'] = ['id' => $problem['location']['id'], 'name' => $problem['location']['title']]; 
		$problem['assigned'] = ['id' => $problem['assigned_to']['id'], 'name' => $problem['assigned_to']['first_name'] .' '. $problem['assigned_to']['last_name'], 'email' => $problem['assigned_to']['email']];
		$attach_assets = [];
		unset($problem['impact_id'], $problem['status_type_id'], $problem['location_type_id'], $problem['priority_id'], $problem['assigned_id'], $problem['agent_id'], $problem['assigned_to'], $problem['group_id']);

		return $problem;

	}

	/**
	* Function to get change
	* @param SdProblem $problem
	* @return Response
	*/
	public function getChange(SdProblem $problem)
	{
		$change = $problem->attachChange()->with([
			'requester:id,email',
			'changeType:id,name',
			'department:id,name',
			'status:id,name',
			'priority:id,name',
			'location:id,title',
			'impactType:id,name',
			'team:id,name',
		])->first()->toArray();

		$change = $this->getChangeFormatProblem($change);
		return successResponse('', $change);
	}

	/**
	* Function to format problem
	* @param array $change
	* @return array $change
	*/
	private function getChangeFormatProblem($change)
	{
		unset($change['requester_id'],$change['department_id'], $change['description'], $change['status_type_id'], $change['priority_id'], $change['impact_id'], $change['location_type_id'], $change['group_id'], $change['assigned_id'],$change['status_id'],$change['location_id'],$change['change_type_id'],$change['team_id'],$change['approval_id'],$change['created_at'],$change['updated_at'],$change['pivot'],$change['change_type'],$change['team'],$change['location'],$change['impact_type'],$change['attach_assets']);

		return $change;
	}

	/**
	* Function to get assets
	* @param SdProblem $problem
	* @return Response
	*/
	public function getAssets(SdProblem $problem)
	{
		$problem = $problem->where('id',$problem->id)->with('attachAssets')->first()->toArray();

		$problem = $this->getAssetsFormatProblem($problem);

		return successResponse('', $problem);

	}

	/**
	* Function to format problem
	* @param array $Problem
	* @return array $problem
	*/
	private function getAssetsFormatProblem($problem)
	{
		$problem['asset_ids'] = [];
		//dd($problem);
		foreach ($problem['attach_assets'] as &$asset) {
			// foreach ((array) $assets['asset'] as &$asset) {
				$manage = User::find($asset['managed_by']);
				$use = User::find($asset['used_by']);
				$problem['asset_ids'][] = ['id' => $asset['id'], 'name' => $asset['name'], 'managed_by_id' => ['id' => $manage->id,
					'name' => $manage->first_name .' '. $manage->last_name], 'used_by_id' => ['id' => $use->id, 'name' => $use->first_name
					. ' ' . $use->last_name
				]];
			// }
		}

		unset($problem['subject'], $problem['department_id'], $problem['description'], $problem['status_type_id'], $problem['priority_id'], $problem['impact_id'], $problem['location_type_id'], $problem['group_id'], $problem['agent_id'], $problem['assigned_id'], $problem['attach_assets']);

		return $problem;
	}

	/**
	* Function to get tickets
	* @param $problemId
	* @param request
	* @return Response
	*/
	public function getTickets(SdProblem $problem,Request $request)
	{	
		$searchString = $request->search_query;
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc';

		$tickets = $problem
            ->attachTickets()
            ->with([
            	'assigned:id,user_name,first_name,last_name,profile_pic,email',
            	'firstThread:id,ticket_id,title',
        		'types:id,name'])
            ->where(function ($query) use ($searchString) {
                $query
                    ->where('ticket_number', 'LIKE', "%$searchString%")
                    ->orWhereHas('firstThread', function ($query) use ($searchString) {
                        $query->where('title', 'LIKE', "%$searchString%");
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

		$tickets = $this->getTicketsFormatProblem($tickets);

		return successResponse('', $tickets);

	}

	/**
	* Function to format problem
	* @param array $tickets
	* @return array $tickets
	*/
	private function getTicketsFormatProblem($tickets)
	{
		$tickets['tickets'] = [];
        foreach ($tickets['data'] as $ticketData) {
            $ticket = [
                'id' => $ticketData['id'],
                'ticket_number' => $ticketData['ticket_number'],
                'subject' => $ticketData['first_thread']['title'],
                'assignedTo' => $ticketData['assigned'],
                'type' => $ticketData['types']
            ];
            array_push($tickets['tickets'], $ticket);
        }
        unset($tickets['data']);

        return $tickets;
	}

	/**
	* Function to detach ticket
	* @param SdProblem $problem
	* @param Tickets $ticket
	* @return Response
	*/
	public function detachTicket(SdProblem $problem, Tickets $ticket)
	{
        $problem->attachTickets()->where('ticket_id', $ticket->id)->detach();

        return successResponse(trans('ServiceDesk::lang.detached_successfully'));
	}

		/**
	* Function to attach asset
	* @param SdProblem $problem
	* @param $request
	* @return 
	*/
	public function attachAsset(SdProblem $problem, Request $request)
	{
		$assetIds = [];
          	foreach($request->input('asset_ids', []) as $assetId) {
            $assetIds[$assetId] = ['type' => 'sd_problem'];
          }

        $problem->attachAssets()->attach($assetIds);

		return successResponse(trans('ServiceDesk::lang.assets_attached_successfully'));
	}

	/**
	* Function to detach asset
	* @param SdProblem $problem
	* @param SdAssets $asset
	* @return Response
	*/
	public function detachAsset(SdProblem $problem, SdAssets $asset)
	{
        $problem->attachAssets()->where('asset_id', $asset->id)->detach();

        return successResponse(trans('ServiceDesk::lang.asset_detached_successfully'));

	}

	/**
	* Function to delete problem
	* @param SdProblem $problem
	* @return Response
	*/
	public function deleteProblem(SdProblem $problem)
	{
    	$problem->attachAssets()->detach();

    	$problem->attachTickets()->detach();

   		$problem->delete();

        return successResponse(trans('ServiceDesk::lang.problem_deleted_successfully'));
	}

	/**
	* Function to attach ticket
	* @param SdProblem $problem
	* @param $request
	* @return Response
	*/
	
	public function attachTicket(SdProblem $problem,Request $request)
	{
		$ticketIds = [];
          foreach($request->input('ticket_ids', []) as $ticketId) {
            $ticketIds[$ticketId] = ['type' => 'sd_problem'];
          }

        $problem->attachTickets()->attach($ticketIds);

		return successResponse(trans('ServiceDesk::lang.tickets_attached_successfully'));
	}

	/**
	* Function to attach ticket
	* @param SdProblem $problem
	* @param Tickets $ticket
	* @return Response
	*/
	public function attachTickets(Tickets $ticket, SdProblem $problem)
	{
		$problem->attachTickets()->attach([$ticket->id => ['type' => 'sd_problem']]);

		return successResponse(trans('ServiceDesk::lang.problem_attached_successfully'));
	}

	/**
     * method for problems index blade page
     * @return view
     */
    public function problemsIndexPage()
    {
        if (!$this->agentPermission->problemsView()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::problem.index');
    }

	/**
	* Function to attach change
	* @param SdProblem $problem
	* @param SdChanges $change
	* @return 
	*/
	public function attachChange(SdProblem $problem,SdChanges $change)
	{	
		$problem->attachChange()->sync($change->id);

		return successResponse(trans('ServiceDesk::lang.change_attached_successfully'));
	}

	/**
	* Function to detach change
	* @param SdProblem $problem
	* @param SdChanges $change
	* @return Response
	*/
	public function detachChange(SdProblem $problem, SdChanges $change)
	{
        $problem->attachChange()->where('change_id', $change->id)->detach();

        return successResponse(trans('ServiceDesk::lang.change_detached_successfully'));

	}

	/**
     * method for problem create blade page
     * @return view
     */
    public function problemCreatePage()
    {
        if (!$this->agentPermission->problemCreate()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::problem.create');
    }

    /**
     * method for problem edit blade page
     * @param $problemId
     * @return view
     */
    public function problemEditPage($problemId)
    {
        if (!$this->agentPermission->problemEdit()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::problem.edit', compact('problemId'));
    }

    /**
     * method to update problem status to close
     * @param SdProblem $problem
     * @return response
     */
    public function updateProblemStatusToClose(SdProblem $problem)
    {
        $this->changeProblemStatusToClose($problem);

        return successResponse(trans('ServiceDesk::lang.problem_saved_successfully'));
    }

    /**
     * method to get problem activity log
     * @param $problemId
     * @param Request $request
     * @return response
     */
    public function getProblemActivityLog($problemId, Request $request)
    {
        $problem = SdProblem::where('id', $problemId);
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'id';
        $sortOrder = $request->input('sort-order') ?: 'desc';
        $page = $request->page ?: 1;

        $problemActivityLogs = $this->problemActivityLog($problemId,$limit,$sortField,$sortOrder,$page);
        
       	$this->formatProblemActivityLog($problemActivityLogs);
       	return successResponse('', ['problem_activity_logs' => $problemActivityLogs]);
    }

    /**
     * method to get problem activity log
     * @param $problemId
     * @param $limit
     * @param $sortField
     * @param $sortOrder
     * @param $page
     * @return $problemActivityLogs
     */
    private function problemActivityLog($problemId,$limit,$sortField,$sortOrder,$page)
    {
    	$problemActivityLogs = ActivityBatch::with(['activity.creator:id,first_name,last_name,user_name,email,profile_pic'])->whereHas('activity', function($query) use($problemId)
            {
                $query->where([['source_type', 'App\Plugins\ServiceDesk\Model\Problem\SdProblem'], ['source_id', $problemId]]);
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();
        return $problemActivityLogs;
    }
    /**
     * method to format problem activity log
     * @param array $prblemActivityLogs
     * @return null
     */
    private function formatProblemActivityLog(array &$problemActivityLogs)
    {
        $activity = '';
        $problemActivity = [];
        foreach ($problemActivityLogs['data'] as $activityLogs) {
            $activityLogs = $activityLogs['activity'];
            $activity = ($activityLogs[0]['event_name'] == 'created' && $activityLogs[0]['log_name'] == 'problem') ? "Created a new Problem <b>({$activityLogs[0]['source_id']})</b>, " : '';
            if ($activityLogs[0]['log_name'] == 'problem_pivot') {
                $this->formatProblemPivotActivityLog($activityLogs, $problemActivity);
                continue;
            }
            $this->formatActivity($activityLogs, $activity);
            $activity = rtrim($activity, ', ');
            $problemActivity[] = ['id' => $activityLogs[0]['id'], 'creator' => $activityLogs[0]['creator'], 'name' => $activity, 'created_at' => $activityLogs[0]['created_at']];
        }
        $problemActivityLogs['data'] = $problemActivity;
    }

    /** 
     * method to format activity
     * @param array $activityLogs
     * @param $activity
     * @return $problemActivityLog/null
     */
    private function formatActivity(array &$activityLogs, string &$activity)
    {
        foreach ($activityLogs as &$problemActivityLog) {
            $tag = $problemActivityLog['field_or_relation'] == 'description' ? 'br' : 'b';
            switch ($problemActivityLog['field_or_relation']) 
            {	
           		case "requester":
           		    $requester = json_decode($problemActivityLog['final_value']);
                	$requesterUrl = Config::get('app.url')."/user/{$requester->id}";
                	$problemActivityLog['final_value'] = "<a href={$requesterUrl}>{$requester->full_name}</a>";
                	break;
                case "assignedTo":
                	$assignedTo = json_decode($problemActivityLog['final_value']);
                	$assignedToUrl = Config::get('app.url')."/user/{$assignedTo->id}";
                	$problemActivityLog['final_value'] = "<a href={$assignedToUrl}>{$assignedTo->full_name}</a>";
                	break;
                case "attachment":
                	return $this->formatProblemAttachmentActvityLog($problemActivityLog, $activity);
            }
            if ($activityLogs[0]['log_name'] == 'sd_problem:popup_update') {
	            $this->formatPopUpActvityLog($problemActivityLog, $activity);
	            continue;
        	}
            if(($problemActivityLog['event_name'] == 'created' or $problemActivityLog['event_name'] == 'updated') and $problemActivityLog['log_name'] == 'problem')
            {
                $activity = $activity . 'set ' . implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation']))) . " as <{$tag}>{$problemActivityLog['final_value']}</{$tag}>, ";
            }
        }
    }

    /** 
     * method to format problem attachment activity
     * @param array $problemActivityLog
     * @param $activity
     * @return null
     */
    private function formatProblemAttachmentActvityLog($problemActivityLog, &$activity)
    {	
    	$fieldOrRelation = implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation'])));
    	$activity = implode('',[$activity ,$problemActivityLog['event_name'], ' ', $fieldOrRelation]);
    }

    /** 
     * method to format popup activity
     * @param array $problemActivityLogs
     * @param $activity
     * @return null
     */
    private function formatPopUpActvityLog(array &$problemActivityLog, string &$activity)
    {	$added = 'added';
    	if( $problemActivityLog['event_name'] == 'updated')
    		$added = 'updated';
    	if($problemActivityLog['event_name'] == 'created' or $problemActivityLog['event_name'] == 'updated' and $problemActivityLog['log_name'] == 'sd_problem:popup_update' and ($problemActivityLog['field_or_relation'] == 'solution' or $problemActivityLog['field_or_relation'] == 'root-cause' or $problemActivityLog['field_or_relation'] == 'symptoms' or $problemActivityLog['field_or_relation'] == 'impact')) 
                {
	                	$activity = $activity . ''.$added. ' the Problem ' . implode(' ', array_map('ucfirst', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation']))) . " ";
                }
        elseif($problemActivityLog['event_name'] == 'deleted') {
            if($problemActivityLog['log_name'] == 'sd_problem:popup_update' and ($problemActivityLog['field_or_relation'] == 'solution' or $problemActivityLog['field_or_relation'] == 'root-cause' or $problemActivityLog['field_or_relation'] == 'symptoms' or $problemActivityLog['field_or_relation'] == 'impact'))
                {
                	$activity = $activity . 'deleted the Problem ' . implode(' ', array_map('ucfirst', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation']))) . " ";
                }
            if($problemActivityLog['field_or_relation'] == 'attachment')
				{
					$activity = $activity . 'deleted ' . implode(' ', array_map('ucfirst', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation']))) . " ";
				}
        }
    }
    /**
     * method to format problem pivot activity log
     * @param Activity $problemActivityLogs
     * @param array $problemActivity
     * @return null
     */
    private function formatProblemPivotActivityLog($problemActivityLogs, array &$problemActivity)
    {
        switch($problemActivityLogs[0]['field_or_relation'])
        {
            case 'attachChange':
                return $this->formatChangeAttachedToProblemActivityLog($problemActivityLogs, $problemActivity);
            case 'attachAssets':
                return $this->formatAssetsAttachedToproblemActivityLog($problemActivityLogs, $problemActivity);
            case 'attachTickets':
                return $this->formatTicketsAttachedToProblemActivityLog($problemActivityLogs, $problemActivity);
            default:
                return null;
        }
    }

    /**
     * method to format assets attached to problem activity log
     * @param Activity $problemActivityLogs
     * @param array $problemActivity
     * @return null
     */
    private function formatAssetsAttachedToProblemActivityLog($problemActivityLogs, array &$problemActivity)
    {
        $assetNames = '';
        foreach ($problemActivityLogs as $problemActivityLog) {
            $asset = SdAssets::find($problemActivityLog['final_value']);
            $assetNames = $assetNames . '<a href='.faveoUrl("service-desk/assets/{$asset->id}/show").'>'."<b>(#AST-{$asset->id}) {$asset->name}</b></a>, ";
        }
        $assetNames = rtrim($assetNames, ', ');
        $activity = "{$problemActivityLogs[0]['event_name']} asset $assetNames";
        $problemActivity[] = ['id' => $problemActivityLogs[0]['id'], 'creator' => $problemActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $problemActivityLogs[0]['created_at']];
    }

    /**
     * method to format change attached to problem activity log
     * @param Activity $problemActivityLogs
     * @param array $problemActivity
     * @return null
     */
    private function formatChangeAttachedToProblemActivityLog($problemActivityLogs, array &$problemActivity)
    {
        $changeNames = '';
        foreach ($problemActivityLogs as $problemActivityLog) {
            $change = SdChanges::find($problemActivityLog['final_value']);
            $changeNames = $changeNames . '<a href='.faveoUrl("service-desk/changes/{$change->id}/show").'>'."<b>(#CHN-{$change->id}) {$change->name}</b></a>, ";
        }
        $changeNames = rtrim($changeNames, ', ');
        $activity = "{$problemActivityLogs[0]['event_name']} change $changeNames";
        $problemActivity[] = ['id' => $problemActivityLogs[0]['id'], 'creator' => $problemActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $problemActivityLogs[0]['created_at']];
    }

    /**
     * method to format tickets attached to problem activity log
     * @param Activity $problemActivityLogs
     * @param array $problemActivity
     * @return null
     */
    private function formatTicketsAttachedToProblemActivityLog($problemActivityLogs, array &$problemActivity)
    {
        $ticketNames = '';
        foreach ($problemActivityLogs as $problemActivityLog) {
            $ticket = Tickets::find($problemActivityLog['final_value']);
            $ticketNames = $ticketNames . '<a href='.faveoUrl("thread/{$ticket['id']}").'>'. "<b>({$ticket['ticket_number']}) {$ticket['first_thread']['title']}</b></a>, ";
        }
        $ticketNames = rtrim($ticketNames, ', ');
        $activity = "{$problemActivityLogs[0]['event_name']} ticket $ticketNames";
        $problemActivity[] = ['id' => $problemActivityLogs[0]['id'], 'creator' => $problemActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $problemActivityLogs[0]['created_at']];
    }

    /**
     * method to change problem status to close
     * @param SdProblem $problem
     * @return null
     */
    public function changeProblemStatusToClose(SdProblem $problem)
    {
        $closedStatusId = TicketType::where('name', 'Closed')->first()->id;
        $problem->update(['status_type_id' => $closedStatusId]);
        $tickets = $problem->attachTickets()->get();
        $ticketController = new TicketController();
        foreach ($tickets as $ticket) {
        	$ticketController->changeStatus($ticket->id, $closedStatusId);
        }
    }

    /**
     * method to get Planning Popups
     * @param $problemId
     * @return success response
     */
    public function planningPopups($problemId)
    {
        $owner = "sd_problem:{$problemId}";
        $planningPopups = GeneralInfo::where('owner',$owner)->get()->toArray();

        foreach ($planningPopups as &$planningPopup) {
            $planningPopup['description'] = $planningPopup['value'];
            $planningPopup['attachment'] = Attachments::where('owner', "sd_problem:{$planningPopup['key']}:{$problemId}")->first();
            unset($planningPopup['value']);
        }
        
        $planningPopups =  $this->formatPlanningPopups($planningPopups);
		
        return successResponse('', ['planning_popups' => $planningPopups]);
    }

    /**
     * method to format planningPopups
     * @param $planningPopups
     * @return $planningPopups
     */
    private function formatPlanningPopups($planningPopups)
    {
    	foreach($planningPopups as $key => &$planningPopup) { 
    		if($planningPopup['key'] == 'solution-title')
    		{
    			$title = $planningPopup['description'];
    			unset($planningPopups[$key]);
    	}}
    	foreach($planningPopups as &$planningPopup) { 
    		if($planningPopup['key'] == 'solution')
    			$planningPopup['title'] = $title;
    	}

    	$planningPopups = array_values($planningPopups);
    	
        return $planningPopups;
    }
}