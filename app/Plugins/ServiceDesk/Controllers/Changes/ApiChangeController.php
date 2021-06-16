<?php
namespace App\Plugins\ServiceDesk\Controllers\Changes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use File;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Request\Change\CreateUpdateChangeRequest;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\User;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use Config;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Plugins\ServiceDesk\Request\Change\AttachTicketToChangeRequest;
use App\Model\helpdesk\Ticket\Tickets as Ticket;

/**
 * Handles API's for Change Controller
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiChangeController extends BaseServiceDeskController {
    
    // managing agent permission
    protected $agentPermission;

    public function __construct() {
        $this->middleware('role.agent');
        $this->agentPermission = new AgentPermissionPolicy();
    }

    /**
     * method for change create blade page
     * @return view
     */
    public function changeCreatePage()
    {
        if (!$this->agentPermission->changeCreate()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::changes.create');
    }

    /**
     * method for change edit blade page
     * @return view
     */
    public function changeEditPage($changeId)
    {
        if (!$this->agentPermission->changeEdit()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::changes.edit', compact('changeId'));
    }

    /**
     * method to create or update change
     * @param CreateUpdateChangeRequest $request
     * @return response
     */
    public function createUpdateChange(CreateUpdateChangeRequest $request)
    {
        if (!$this->agentPermission->changeCreate()) {
            return errorResponse(trans('ServiceDesk::lang.permission-denied'));
        }

        $change = $request->toArray();
        $this->makeEmptyAttributesNullable($change);

        $isCabAppliedPending = $this->checkCabAppliedOnChange($request);
        if ($isCabAppliedPending) {
          return errorResponse(trans('ServiceDesk::lang.cab_approval_under_progress'));
        }

        $changeObject = SdChanges::updateOrCreate(['id' => $request->id], $change);
        $changeId = $changeObject->id;

        $assetIds = [];
        if (isset($change['asset_ids'])) {
          foreach ($change['asset_ids'] as $assetId) {
            $assetIds[$assetId] = ['type' => 'sd_changes'];
          }
          $changeObject->attachAssets()->sync($assetIds);
        }
    
        if ($request->has('ticket_id')) {
            $changeObject->attachTickets()->attach([$change['ticket_id'] => ['type' => 'initiated']]);
        }

        if ($request->has('problem_id')) {
            $changeObject->attachProblems()->attach($change['problem_id']
                );
            $changeObject->attachProblems()->first()->manuallyLogActivityForPivot($changeObject->attachProblems()->first(), 'attachChange', $changeObject->id, 'attached', null, 'problem_pivot', 1, null);
        }

        $this->fillAttachment($request, $changeId);

        return successResponse(trans('ServiceDesk::lang.change_saved_successfully'));
    }

    /**
     * method to fill attachment
     * @param file $request
     * @param $changeId
     * @return void
     */
    private function fillAttachment($request, $changeId)
    {
        $attachment = Attachments::where('owner', 'sd_changes:' . $changeId)->first();
        if ($request->attachment_delete || ($request->file('attachment') && $attachment))  {
            $file = $attachment->value;
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            Attachments::where('owner', 'sd_changes:' . $changeId)->delete();
        }

        if ($request->file('attachment')) {
            $attachments[] = $request->file('attachment');
            UtilityController::attachment($changeId, 'sd_changes', $attachments);
        }
    }

    /**
     * method to get change based on change id
     * @param changeId
     * @return response
     */
    public function getChange($changeId)
    {
        $baseQuery = SdChanges::where('id', $changeId);
        if ($baseQuery->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.change_not_found'));
        }
       
        $change = $this->getChangeQueryAndReturnChangeData($baseQuery);

        return successResponse('', ['change' => $change]);
    }

    /**
     * method to get get change query and return change data
     * @param QueryBuilder $baseQuery
     * @return array $change
     */
    public function getChangeQueryAndReturnChangeData(QueryBuilder $baseQuery)
    {
        $change = $baseQuery->with([
            'requester:id,first_name,last_name,email,profile_pic',
            'status:id,name',
            'priority:id,name',
            'changeType:id,name',
            'impactType:id,name',
            'location:id,title as name',
            'team:id,name',
            'department:id,name',
            'attachAssets:sd_assets.id,name',
            'attachReleases' => function ($query) {
                $query->with([
                    'status:id,name',
                    'priority:id,name',
                    'releaseType:id,name',
                    'location:id,title as name',
                ]);
            }
        ])
        ->first()->toArray();
        $this->formatChange($change);

        return $change;
    }

    /**
     * method to format change
     * @param array $change
     * @return null
     */
    private function formatChange(&$change)
    {
        $change['requester']['name'] = $change['requester']['full_name'];
        $change['assets'] = $change['attach_assets'];
        $change['attach_releases'] = $this->formatRelease($change['attach_releases']);
        $change['attachment'] = Attachments::where('owner', 'sd_changes:' . $change['id'])->first();
        unset($change['status_id'], $change['priority_id'], $change['change_type_id'], $change['impact_id'], $change['requester']['first_name'], $change['requester']['last_name'], $change['requester']['full_name'], $change['requester']['meta_name'], $change['attach_assets']);
    }

    /** 
     * method to format release
     * @param array $release
     * @return array $release
     */
    private function formatRelease($release)
    {
        if (array_key_exists('location_relation', $release)) {
            $release['location'] = $release['location_relation'];
            unset($release['location_id'], $release['location_relation']);
        }
        unset($release['status_id'], $release['priority_id'], $release['release_type_id']); 

        return $release;
    }

    /**
     * method to delete change
     * @param SdChanges $change
     * @return response
     */
    public function deleteChange(SdChanges $change)
    {
        $change->delete();

        return successResponse(trans('ServiceDesk::lang.change_deleted_successfully'));
    }

    /**
     * method to attach assets
     * @param $changeId
     * @param $request (contains asset_ids)
     * @return response
     */
    public function attachAssets($changeId, Request $request)
    {
        $change = SdChanges::where('id', $changeId);

        if ($change->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.change_not_found'));
        }

        $assetIds = [];
        if ($request->has('asset_ids')) {
          foreach ((array) $request->asset_ids as $assetId) {
            $assetIds[$assetId] = ['type' => 'sd_changes'];
          }
        }

        $change->first()->attachAssets()->sync($assetIds);

        return successResponse(trans('ServiceDesk::lang.assets_attached_successfully'));
    }

    /**
     * method to detach assets
     * @param $changeId
     * @param $assetId
     * @return response
     */
    public function detachAsset($changeId, $assetId)
    {
        $change = SdChanges::where('id', $changeId);

        if ($change->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.change_not_found'));
        }

        $asset = SdAssets::find($assetId);

        if (!$asset) {
            return errorResponse(trans('ServiceDesk::lang.asset_not_found'));
        }

        $change->first()->attachAssets()->where('asset_id', $assetId)->detach();

        return successResponse(trans('ServiceDesk::lang.asset_detached_successfully'));
    }

    /**
     * method to attach release
     * @param $changeId
     * @param $releaseId
     * @return response
     */
    public function attachRelease($changeId, $releaseId)
    {
        $release = SdReleases::where('id', $releaseId);
        $change = SdChanges::where('id', $changeId);

        if ($change->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.change_not_found'));
        }

        if ($release->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.release_not_found'));
        }

        $change->first()->attachReleases()->sync($releaseId);

        return successResponse(trans('ServiceDesk::lang.release_attached_successfully'));
    } 
    
    /**
     * method to detach release
     * @param $changeId
     * @return response
     */
    public function detachRelease($changeId)
    {
        $change = SdChanges::where('id', $changeId);

        if ($change->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.change_not_found'));
        }

        $change->first()->attachReleases()->detach();

        return successResponse(trans('ServiceDesk::lang.release_detached_successfully'));
    }

    /**
     * method to update change status to close
     * @param $changeId
     * @return response
     */
    public function updateChangeStatusToClose($changeId)
    {

        $change = SdChanges::where('id', $changeId);

        if ($change->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.change_not_found'));
        }

        $closedStatusId = SdChangestatus::where('name', 'Closed')->first()->id;
        $change->update(['status_id' => $closedStatusId]);
        $change = $change->first();
        $change->manuallyLogActivityForPivot($change, 'status', $change->status->name, 'updated', '', 'change');

        return successResponse(trans('ServiceDesk::lang.change_saved_successfully'));
    }

    /**
     * method to get change activity log
     * @param $changeId
     * @param Request $request
     * @return response
     */
    public function getChangeActivityLog($changeId, Request $request)
    {
        $change = SdChanges::where('id', $changeId);
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'id';
        $sortOrder = $request->input('sort-order') ?: 'desc';
        $page = $request->page ?: 1;

        $changeActivityLogs = ActivityBatch::with(['activity.creator:id,first_name,last_name,user_name,email,profile_pic'])->whereHas('activity', function($query) use($changeId)
            {
                $query->where([['source_type', 'App\Plugins\ServiceDesk\Model\Changes\SdChanges'], ['source_id', $changeId]]);
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatChangeActivityLog($changeActivityLogs);

        return successResponse('', ['change_activity_logs' => $changeActivityLogs]);
    }

    /**
     * method to format change activity log
     * @param array $changeActivityLogs
     * @return null
     */
    private function formatChangeActivityLog(array &$changeActivityLogs)
    {
        $activity = '';
        $changeActivity = [];
        foreach ($changeActivityLogs['data'] as $activityLogs) {
            $activityLogs = $activityLogs['activity'];
            $activity = ($activityLogs[0]['event_name'] == 'created' && $activityLogs[0]['log_name'] == 'change') ? "created a new change <b>(#CHN-{$activityLogs[0]['source_id']})</b>, " : '';
            if ($activityLogs[0]['log_name'] == 'change_pivot') {
                $this->formatChangePivotActivityLog($activityLogs, $changeActivity);
                continue;
            }elseif ($activityLogs[0]['log_name'] == 'change_cab') {
                $changeActivity[] = ['id' => $activityLogs[0]['id'], 'creator' => $activityLogs[0]['creator'], 'name' => $activityLogs[0]['final_value'], 'created_at' => $activityLogs[0]['created_at']];
                continue;
            }
            $this->formatActivity($activityLogs, $activity);
            $activity = rtrim($activity, ', ');
            $changeActivity[] = ['id' => $activityLogs[0]['id'], 'creator' => $activityLogs[0]['creator'], 'name' => $activity, 'created_at' => $activityLogs[0]['created_at']];
        }
        $changeActivityLogs['data'] = $changeActivity;
    }

    /** 
     * method to format activity
     * @param array $activityLogs
     * @param $activity
     * @return null
     */
    private function formatActivity(array &$activityLogs, string &$activity)
    {
        foreach ($activityLogs as &$changeActivityLog) {
                $tag = $changeActivityLog['field_or_relation'] == 'description' ? 'br' : 'b';
                if ($changeActivityLog['field_or_relation'] == 'requester')
                {
                    $requester = json_decode($changeActivityLog['final_value']);
                    $requesterUrl = Config::get('app.url')."/user/{$requester->id}";
                    $changeActivityLog['final_value'] = "<a href={$requesterUrl}>{$requester->full_name}</a>";
                }
                $activity = $activity . 'set ' . implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $changeActivityLog['field_or_relation']))) . " as <{$tag}>{$changeActivityLog['final_value']}</{$tag}>, ";
            }
    }

    /**
     * method to format change pivot activity log
     * @param Activity $changeActivityLogs
     * @param array $changeActivity
     * @return null
     */
    private function formatChangePivotActivityLog($changeActivityLogs, array &$changeActivity)
    {
        switch($changeActivityLogs[0]['field_or_relation'])
        {
            case 'attachReleases':
                $this->formatReleaseAttachedToChangeActivityLog($changeActivityLogs, $changeActivity);
                break;
            case 'attachAssets':
                $this->formatAssetsAttachedToChangeActivityLog($changeActivityLogs, $changeActivity);
                break;
            case 'attachTickets':
                $this->formatTicketsAttachedToChangeActivityLog($changeActivityLogs, $changeActivity);
                break;
            default:
                return false;
        }
    }

    /**
     * method to format release attached to change activity log
     * @param Activity $changeActivityLogs
     * @param array $changeActivity
     * @return null
     */
    private function formatReleaseAttachedToChangeActivityLog($changeActivityLogs, array &$changeActivity)
    {
        $release = SdReleases::find($changeActivityLogs[0]['final_value']);
        $activity = "{$changeActivityLogs[0]['event_name']} release ". '<a href='.faveoUrl("service-desk/releases/{$release->id}/show").'>'. "<b>(#REL-{$release->id}) {$release->subject}</b></a>";
        $changeActivity[] = ['id' => $changeActivityLogs[0]['id'], 'creator' => $changeActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $changeActivityLogs[0]['created_at']];


    }

    /**
     * method to format assets attached to change activity log
     * @param Activity $changeActivityLogs
     * @param array $changeActivity
     * @return null
     */
    private function formatAssetsAttachedToChangeActivityLog($changeActivityLogs, array &$changeActivity)   
    {
        $assetNames = '';
        foreach ($changeActivityLogs as $changeActivityLog) {
            $asset = SdAssets::find($changeActivityLog['final_value']);
            $assetNames = $assetNames . '<a href='.faveoUrl("service-desk/assets/{$asset->id}/show").'>'."<b>(#AST-{$asset->id}) {$asset->name}</b></a>, ";
        }
        $assetNames = rtrim($assetNames, ', ');
        $activity = "{$changeActivityLogs[0]['event_name']} asset $assetNames";
        $changeActivity[] = ['id' => $changeActivityLogs[0]['id'], 'creator' => $changeActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $changeActivityLogs[0]['created_at']];
    }

    /**
     * method to check cab applied on change or not
     * if cab is applied on change and cab is in pending status
     * then don't let agent/admin to change change status
     * @param  CreateUpdateChangeRequest $request
     * @return bool $isCabAppliedPending
     */
    private function checkCabAppliedOnChange(CreateUpdateChangeRequest $request)
    {
      $isCabAppliedPending = SdApprovalWorkflowChange::where([['change_id', $request->id],['status', 'PENDING']])->count();

      return (bool) $isCabAppliedPending;
    }

    /**
     * method to get planning popup details (reason for change, impact, rollout plan, backout plan)
     * @param  int $changeId
     * @return response
     */
    public function planningPopups($changeId)
    {
        $owner = "sd_changes:{$changeId}";
        $planningPopups = GeneralInfo::where('owner',$owner)->get()->toArray();

        foreach ($planningPopups as &$planningPopup) {
            $planningPopup['description'] = $planningPopup['value'];
            $planningPopup['attachment'] = Attachments::where('owner', "sd_changes:{$planningPopup['key']}:{$changeId}")->first();
            unset($planningPopup['value']);
        }

        return successResponse('', ['planning_popups' => $planningPopups]);
    }

    /**
     * method to attach change to ticket
     * @param AttachTicketToChangeRequest $request (includes parameters : changeId, ticketId and type)
     * @return response
     */
    public function attachChangeToTicket(AttachTicketToChangeRequest $request)
    {
        $this->attachTicketAndChange($request);
        return successResponse(trans('ServiceDesk::lang.change_attached_successfully'));
    } 

     /**
     * method to attach ticket to change
     * @param AttachTicketToChangeRequest $request (includes parameters : changeId, ticketId and type)
     * @return response
     */
    public function attachTicketToChange(AttachTicketToChangeRequest $request)
    {
        $this->attachTicketAndChange($request);
        return successResponse(trans('ServiceDesk::lang.tickets_attached_successfully'));
    }

    /**
     * method to attach ticket and change
     * @param AttachTicketToChangeRequest $request
     * @return null
     */
    private function attachTicketAndChange(AttachTicketToChangeRequest $request)
    {
        $ticketIds = $request->ticket_id ?: $request->ticket_ids;
        $type = $request->type;
        $changeId = $request->change_id;

        $attachableTicketIds = [];
        if ($ticketIds) {
          foreach ((array) $ticketIds as $ticketId) {
            $attachableTicketIds[$ticketId] = ['type' => $type];
          }
        }

        $change = SdChanges::find($changeId);

        $change->attachTickets()->attach($attachableTicketIds);
    }
    
    /**
     * method to detach change to ticket
     * @param AttachTicketToChangeRequest $request (includes parameters : changeId, ticketId and type)
     * @return response
     */
    public function detachChangeFromTicket(AttachTicketToChangeRequest $request)
    {
        $this->detachChangeAndTicket($request);

        return successResponse(trans('ServiceDesk::lang.change_detached_successfully'));
    }

    /**
     * method to detach change and ticket
     * @param AttachTicketToChangeRequest $request (includes parameters : changeId, ticketId and type)
     * @return response
     */
    private function detachChangeAndTicket(AttachTicketToChangeRequest $request)
    {
        $ticketId = $request->ticket_id;
        $changeId = $request->change_id;
        $type = $request->type;
        $change = SdChanges::find($changeId);

        $change->attachTickets()->wherePivot('ticket_id', $ticketId)->wherePivot('type', $type)->detach();
    }

    /**
     * method to detach ticket from change
     * @param AttachTicketToChangeRequest $request (includes parameters : changeId, ticketId and type)
     * @return response
     */
    public function detachTicketFromChange(AttachTicketToChangeRequest $request)
    {
        $this->detachChangeAndTicket($request);

        return successResponse(trans('ServiceDesk::lang.ticket_detached_successfully'));
    }
    
    /**
     * method to get associated changes linked to ticket
     * @param Request $request
     * @return response
     */
    public function associatedChangesLinkedToTicket(Request $request)
    {
        $ticketId = $request->ticket_id;
        $searchString = $request->search_query;
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc';

        $changes =  SdChanges::where(function($query) use($searchString) {
                        $query
                            ->orWhereRaw("concat('#CHN-',id) LIKE ?", ['%'.$searchString.'%'])
                            ->orWhere('subject', 'LIKE', "%$searchString%");
                        })
                        ->with(['attachTickets' => function ($query) {
                            $query->withPivot('type');
                        }])->WhereHas('attachTickets', function($query) use ($ticketId) {
                            $query->where('ticket_id', $ticketId);
                        })->orderBy($sortField, $sortOrder)
                        ->paginate($limit)
                        ->toArray();


        $this->formatAssociatedChangesLinkedWithTicket($changes);

        return successResponse('', $changes);
    }


    /**
     * method to format changes linked to ticket in ticket ticket inbox page
     * @param array $changes
     * @return null
     */
    private function formatAssociatedChangesLinkedWithTicket(array &$changes)
    {
        $changes['changes'] = [];
        foreach ($changes['data'] as $changeData) {
            $change = [
                'id' => $changeData['id'],
                'change_number' => "#CHN-{$changeData['id']}",
                'subject' => $changeData['subject'],
                'type' => ucfirst(reset($changeData['attach_tickets'])['pivot']['type'])
            ];
            array_push($changes['changes'], $change);
        }
        unset($changes['data']);
    }

    /**
     * method to get associated tickets linked to change
     * @param Request $request
     * @return response
     */
    public function associatedTicketsLinkedToChange(Request $request)
    {
        $changeId = $request->change_id;
        $searchString = $request->search_query;
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc';

        $change =  SdChanges::find($changeId);

        if ($change) {
            $tickets = $change
            ->attachTickets()
            ->with(['assigned:id,user_name,first_name,last_name,profile_pic,email','firstThread:id,ticket_id,title'])
            ->withPivot('type')
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
        }

        $this->formatAssociatedTicketsLinkedWithChange($tickets);

        return successResponse('', $tickets);
    }


    /**
     * method to format tickets linked to changes
     * @param array $tickets
     * @return null
     */
    private function formatAssociatedTicketsLinkedWithChange(array &$tickets)
    {
        $tickets['tickets'] = [];
        foreach ($tickets['data'] as $ticketData) {
            $ticket = [
                'id' => $ticketData['id'],
                'ticket_number' => $ticketData['ticket_number'],
                'subject' => $ticketData['first_thread']['title'],
                'assigned' => $ticketData['assigned'],
                'type' => ucfirst($ticketData['pivot']['type'])
            ];
            array_push($tickets['tickets'], $ticket);
        }
        unset($tickets['data']);
    }

    /**
     * method to format tickets attached or detached in change activity log
     * @param Activity $changeActivityLogs
     * @param array $changeActivity
     * @return null
     */
    private function formatTicketsAttachedToChangeActivityLog($changeActivityLogs, array &$changeActivity)
    {
        $ticketNames = '';
        foreach ($changeActivityLogs as $changeActivityLog) {
            $ticket = Ticket::with('firstThread')->where('id', $changeActivityLog['final_value'])->first()->toArray();
            $ticketNames = $ticketNames . '<a href='.faveoUrl("thread/{$ticket['id']}").'>'. "<b>({$ticket['ticket_number']}) {$ticket['first_thread']['title']}</b></a>, ";
        }
        $ticketNames = rtrim($ticketNames, ', ');
        $activity = "{$changeActivityLogs[0]['event_name']} ticket $ticketNames";
        $changeActivity[] = ['id' => $changeActivityLogs[0]['id'], 'creator' => $changeActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $changeActivityLogs[0]['created_at']];
    }

    /** 
     * method for changes index blade page
     * @return view
     */
    public function changesIndexPage()
    {
        if (!$this->agentPermission->changesView()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::changes.index');
    }

    /** 
     * method for change view blade page
     * @param $changeId
     * @return view
     */
    public function changeViewPage($changeId)
    {
        if (!$this->agentPermission->changesView())
        {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::changes.show', compact('changeId'));
    }

}
