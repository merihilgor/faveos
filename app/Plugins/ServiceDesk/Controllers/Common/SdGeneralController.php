<?php
namespace App\Plugins\ServiceDesk\Controllers\Common;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use File;
use App\Plugins\ServiceDesk\Traits\ShallDeleteAttachmentModel;

/**
 * Class to maintain popup details (in change module and problem module)
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class SdGeneralController extends BaseServiceDeskController
{   
     use ShallDeleteAttachmentModel;
   public function __construct()
    {   
        $this->middleware('auth');
    }

    /**
     * method to store change details, it could be used for problem, release too
     * reason for change, impact, rollout plan or backout plan.
     * @param $modelId (change id)
     * @param $tableName (sd_changes)
     * @param $request (reason, identifier, attachment)
     * @return response
     */
    public function createUpdatePopup($modelId, $tableName, Request $request) 
    {
        $owner = "$tableName:$modelId";
        $attachment = $request->file('attachment');
        $identifier = $request->identifier;
        $description = $request->description;
        $attachedTableName = "$tableName:$identifier";
        GeneralInfo::updateOrCreate([['owner', $owner], ['key', $identifier]], ['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        if($request->has('title'))
        {   
            $titleIdentifer = $request->title;
            GeneralInfo::updateOrCreate([['owner', $owner], ['key', $titleIdentifer]], ['owner' => $owner, 'key' => 'solution-title', 'value' => $request->title]);
        }
        $created = 'created';
        $activity = Activity::get()->where('log_name',$tableName.':popup_update')->where('source_id',$modelId)->where('field_or_relation',$identifier)->where('event_name','created')->toArray();
        if($activity)
        {
            $created = 'updated';
        }
        $generalPath = $this->generalActivity($tableName)::find($modelId);
        $this->attachAttachment($generalPath, $attachedTableName, $request);
        $generalPath->manuallyLogActivityForPivot($generalPath, $identifier, $description, $created, null, "$tableName:popup_update", 0, null);

        return successResponse(trans('ServiceDesk::lang.updated_successfully'));
    }

    public function generalActivity($tableName)
    {
        switch($tableName)
        {
            case "sd_problem":
                $classPath = '\App\Plugins\ServiceDesk\Model\Problem\SdProblem'; 
                 break;
            case "sd_changes":
                $classPath = '\App\Plugins\ServiceDesk\Model\Changes\SdChanges';
                break;
            case "sd_releases":
                $classPath = '\App\Plugins\ServiceDesk\Model\Releases\SdReleases';
                break;
            default:
                return false;
        }
        return $classPath;
    }

    /**
     * method to attach attachment
     * @param $modelId (change id)
     * @param $tableName (table name attached with identifier(reason, rollout-plan, etc..))
     * @param $request (reason, identifier, attachment)
     * @param $tableName
     * @return null
     */
    private function attachAttachment($model, $attachedTableName, $request)
    {
        $attachment = Attachments::where('owner', "$attachedTableName:$model->id")->first();
        if ($this->shallDeleteAttachment($request, $attachment))  {
            $file = $attachment->value;
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            Attachments::where('owner', "$attachedTableName:$model->id")->delete();
            $model->manuallyLogActivityForPivot($model, 'attachment', '', 'deleted', null, 'attachment', 0, null);
        }
        if ($request->file('attachment')) {
            $attachments[] = $request->file('attachment');
            UtilityController::attachment($model->id, $attachedTableName, $attachments);
            $model->manuallyLogActivityForPivot($model, 'attachment', $request->file('attachment'), 'added', null, 'attachment', 0, null);
        }
    }
    
    /**
     * method to get change popup details, it could be used for problem, release too
     * reason for change, impact, rollout plan or backout plan.
     * @param $modelId (change id)
     * @param $tableName (sd_changes)
     * @param $identifier (impact, rollout-plan, backout-plan or reason)
     * @return response
     */
    public function editPopup($modelId, $tableName, $identifier)
    {
        $owner = $tableName . ':' . $modelId;
        $generalInfoQuery = GeneralInfo::where('owner',$owner);
        $generalInfo = $generalInfoQuery->where('key',$identifier)->first();

        if (is_null($generalInfo)) {
            return errorResponse(trans('ServiceDesk::lang.wrong_details'));
        }

        $generalInfoData = [];
            if($identifier == 'solution') {
                $generalInfo->title = GeneralInfo::where('owner',$owner)->where('key','solution-title')->value('value');
            }
            $generalInfo->description = $generalInfo->value;
            $generalInfo->attachment = Attachments::where('owner', "$tableName:$identifier:$modelId")->first();

        $generalInfoData['general_info'] = $generalInfo;

        return successResponse('', $generalInfoData);

    }

    /**
     * method to delete popup details, it could be used for problem, release too
     * reason for change, impact, rollout plan or backout plan.
     * @param $modelId (change id)
     * @param $tableName (sd_changes)
     * @param $identifier (impact, rollout-plan, backout-plan or reason)
     * @return response
     */
    public function deletePopupDetails($modelId, $tableName, $identifier)
    {
        $owner = "{$tableName}:{$modelId}";
        $generalInfo = GeneralInfo::where([['owner', $owner], ['key', $identifier]])->first();

        if (is_null($generalInfo)) {
            return errorResponse(trans('ServiceDesk::lang.wrong_details'));
        }

            if($identifier == 'solution') 
                GeneralInfo::where('owner',$owner)->where('key','solution-title')->first()->delete();

        $generalInfo->delete();
        // need to think of better alternative, as of now cannot pass multiple parameter in delete method to model
        $this->deleteAttachment($modelId, $tableName, $identifier);
        $generalPath = $this->generalActivity($tableName)::find($modelId);
        $generalPath->manuallyLogActivityForPivot($generalPath, $identifier, '', 'deleted', null, "$tableName:popup_update", 0, null);

        return successResponse(trans('ServiceDesk::lang.deleted_successfully'));
    }

    /**
     * method to delete popup attachment it could be used for problem, release 
     * reason for change, impact, rollout plan or backout plan.
     * @param $modelId (change id)
     * @param $tableName (sd_changes)
     * @param $identifier (impact, rollout-plan, backout-plan or reason)
     * @return response
     */
    private function deleteAttachment($attachedModelId, $tableName, $identifier)
    {
        $owner = "{$tableName}:{$identifier}:{$attachedModelId}";
        $attachment = Attachments::where('owner', $owner)->first();
        if ($attachment)  {
            $file = $attachment->value;
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            Attachments::where('owner', $owner)->delete();
        }
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

        $problemActivityLogs = ActivityBatch::with(['activity.creator:id,first_name,last_name,user_name,email,profile_pic'])->whereHas('activity', function($query) use($problemId)
            {
                $query->where([['source_type', 'App\Plugins\ServiceDesk\Model\Problem\SdProblem'], ['source_id', $problemId]]);
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatProblemActivityLog($problemActivityLogs);

        return successResponse('', ['problem_activity_logs' => $problemActivityLogs]);
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
     * @return null
     */
    private function formatActivity(array &$activityLogs, string &$activity)
    {
        foreach ($activityLogs as &$problemActivityLog) {
                $tag = $problemActivityLog['field_or_relation'] == 'description' ? 'br' : 'b';
                if ($problemActivityLog['field_or_relation'] == 'requester')
                {
                    $requester = json_decode($problemActivityLog['final_value']);
                    $requesterUrl = Config::get('app.url')."/user/{$requester->id}";
                    $problemActivityLog['final_value'] = "<a href={$requesterUrl}>{$requester->full_name}</a>";
                }
                if($problemActivityLog['event_name'] == 'created')
                {
                    $activity = $activity . ' added the Problem ' . implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation']))) .' ' ." ";
                }
                else{
                $activity = $activity.' ' .  $problemActivityLog['event_name'].' the Problem ' . implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $problemActivityLog['field_or_relation']))) .' ' ." ";
            }
            }
    }


}
