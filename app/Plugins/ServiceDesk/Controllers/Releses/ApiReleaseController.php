<?php

namespace App\Plugins\ServiceDesk\Controllers\Releses;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use File;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Request\Release\CreateUpdateReleaseRequest;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use App\Plugins\ServiceDesk\Traits\ShallDeleteAttachmentModel;
use App\FaveoStorage\Controllers\StorageController;

class ApiReleaseController extends BaseServiceDeskController {

    protected $agentPermission;
    use ShallDeleteAttachmentModel;

    public function __construct() {
        $this->middleware('auth');
        $this->agentPermission = new AgentPermissionPolicy();
    }

    /**
     * method to create or update release
     * @param CreateUpdateReleaseRequest $request
     * @return response
     */
    public function createUpdateRelease(CreateUpdateReleaseRequest $request)
    {
        $release = $request->toArray();
        $this->makeEmptyAttributesNullable($release);
        $release['planned_start_date'] = convertDateTimeToUtc($request->planned_start_date);
        $release['planned_end_date'] = convertDateTimeToUtc($request->planned_end_date);

        $releaseObject = SdReleases::updateOrCreate(['id' => $request->id], $release);

        $this->attachAssetsToRelease($releaseObject, $request->asset_ids);

        if ($request->has('change_id')) {
            $releaseObject->attachChanges()->sync($release['change_id']);
        }
        
        $this->fillAttachment($request, $releaseObject);

        return successResponse(trans('ServiceDesk::lang.release_saved_successfully'));
    }

    /**
     * method to attach assets to release
     * @param Release $release
     * @param array $assetIds
     * @return null
     */
    private function attachAssetsToRelease($release, $assetIdsFromRequest)
    {
        $assetIds = [];
        if (isset($assetIdsFromRequest)) {
          foreach ($assetIdsFromRequest as $assetId) {
            $assetIds[$assetId] = ['type' => 'sd_releases'];
          }
          $release->attachAssets()->sync($assetIds);
        }
    }

    /**
    * method to fill attachment
    * @param file $request
    * @param $release
    * @return null
    */
    private function fillAttachment($request, $release)
    {   
        $attachment = Attachments::where('owner', 'sd_releases:' . $release->id)->first();
        if ($this->shallDeleteAttachment($request, $attachment))  {
            $file = $attachment->value;
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            Attachments::where('owner', 'sd_releases:' . $release->id)->delete();
            ($request->attachment_delete) ? $release->manuallyLogActivityForPivot($release, 'attachment','', 'deleted', null,'release_attachment', 0, null) : '';
        }
        if ($request->file('attachment')) {
            $attachments[] = $request->file('attachment');
            UtilityController::attachment($release->id, 'sd_releases', $attachments);
            $release->manuallyLogActivityForPivot($release, 'attachment', $request->file('attachment'), 'added', null, 'release_attachment', 0, null);
        }
    }

    /**
     * method to get release based on release id
     * @param release
     * @return response
     */
    public function getRelease(SdReleases $release)
    {
        $release = $release->where('id',$release->id)->with([
            'status:id,name',
            'priority:id,name',
            'releaseType:id,name',
            'location:id,title as name',
            'attachAssets:sd_assets.id,name',
            'attachChanges:sd_changes.id,subject',
        ])
        ->first();
       

        $this->formatRelease($release);
        $this->appendAttachmentsInRelease($release);

        return successResponse('', ['release' => $release]);

    }

    /** 
     * method to format release
     * @param array $release
     * @return void
     */
    private function formatRelease(&$release)
    {
        $releaseObject = SdReleases::find($release['id']);
        
        $release['attachment'] = Attachments::where('owner', 'sd_releases:' . $release['id'])->first();
        unset($release['status_id'], $release['priority_id'], $release['release_type_id']); 
    }

    /**
     * method to attach assets
     * @param $release
     * @param $request (contains asset_ids)
     * @return response
     */
    public function attachAssets(SdReleases $release, Request $request)
    {
        $assetIds = [];
        if ($request->has('asset_ids')) {
            foreach ((array) $request->asset_ids as $assetId) {
                $assetIds[$assetId] = ['type' => 'sd_releases'];
            }
        }

        $release->attachAssets()->attach($assetIds);

        return successResponse(trans('ServiceDesk::lang.assets_attached_successfully'));
    }

    /**
     * method to detach assets
     * @param $release
     * @param $asset
     * @return response
     */
    public function detachAsset(SdReleases $release, SdAssets $asset)
    {
        $release->attachAssets()->where('asset_id', $asset->id)->detach();

        return successResponse(trans('ServiceDesk::lang.asset_detached_successfully'));
    }

    /**
     * Function to mark release as completed
     * @param $release
     * @return Response
     */
    public function markReleaseAsCompleted(SdReleases $release)
    {
        $release->update(['status_id' => 5]);

        return successResponse(trans('ServiceDesk::lang.release_has_completed'));
    }

    /** 
     * method to attach change to release
     * @param  $release
     * @param  $request (contains change_ids)
     * @return Response 
     */
    public function attachChanges(SdReleases $release, Request $request)
    {
        if ($request->has('change_ids')) {
            foreach ((array) $request->change_ids as $changeId) {
                $release->attachChanges()->attach($changeId);
            }
        }

        return successResponse(trans('ServiceDesk::lang.change_attached_successfully'));
    }

    /**
     * method to detach change
     * @param $release
     * @param $change
     * @return response
     */
    public function detachChanges(SdReleases $release, SdChanges $change)
    {
        $release->attachChanges()->wherePivot('change_id','=' ,$change->id)->detach();

        return successResponse(trans('ServiceDesk::lang.change_detached_successfully'));
    }

    /**
     * method to get release activity log
     * @param $release
     * @param Request $request
     * @return response
     */
    public function getReleaseActivityLog(SdReleases $release, Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'id';
        $sortOrder = $request->input('sort-order') ?: 'desc';
        $page = $request->page ?: 1;

        $releaseActivityLogs = ActivityBatch::with(['activity.creator:id,first_name,last_name,user_name,email,profile_pic'])->whereHas('activity', function($query) use($release)
            {
                $query->where([['source_type', 'App\Plugins\ServiceDesk\Model\Releases\SdReleases'], ['source_id', $release->id]]);
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatReleaseActivityLog($releaseActivityLogs);

        return successResponse('', ['release_activity_logs' => $releaseActivityLogs]);
    }

    /**
     * method to format release activity log
     * @param array $releaseActivityLogs
     * @return null
     */
    private function formatReleaseActivityLog(array &$releaseActivityLogs)
    {
        $activity = '';
        $releaseActivity = [];
        foreach ($releaseActivityLogs['data'] as $activityLogs) {
            $activityLogs = $activityLogs['activity'];
            $activity = ($activityLogs[0]['event_name'] == 'created' && $activityLogs[0]['log_name'] == 'release') ? "created a new release <b>(#REL-{$activityLogs[0]['source_id']})</b>, " : '';
            if ($activityLogs[0]['log_name'] == 'release_pivot') {
                $this->formatReleasePivotActivityLog($activityLogs, $releaseActivity);
                continue;
            }
            $this->formatActivity($activityLogs, $activity);
            $activity = rtrim($activity, ', ');
            $releaseActivity[] = ['id' => $activityLogs[0]['id'], 'creator' => $activityLogs[0]['creator'], 'name' => $activity, 'created_at' => $activityLogs[0]['created_at']];
        }
        $releaseActivityLogs['data'] = $releaseActivity;
    }

    /** 
     * method to format activity
     * @param array $activityLogs
     * @param $activity
     * @return null
     */
    private function formatActivity(array &$activityLogs, string &$activity)
    {   
        foreach ($activityLogs as &$releaseActivityLog) {
            $tag = $releaseActivityLog['field_or_relation'] == 'description' ? 'br' : 'b';
            switch ($releaseActivityLog['field_or_relation']) {
                case 'requester':
                    $requester = json_decode($releaseActivityLog['final_value']);
                    $requesterUrl = Config::get('app.url')."/user/{$requester->id}";
                    return $releaseActivityLog['final_value'] = "<a href={$requesterUrl}>{$requester->full_name}</a>";
                case 'attachment':
                    return $this->formatReleaseAttachmentActvityLog($releaseActivityLog, $activity);   
            }
            if ($activityLogs[0]['log_name'] == 'sd_releases:popup_update') {
                $this->formatPopUpActvityLog($releaseActivityLog, $activity);
                continue;
            }
            if(($releaseActivityLog['event_name'] == 'created' or $releaseActivityLog['event_name'] == 'updated') and $releaseActivityLog['log_name'] == 'release')
            {
                if($releaseActivityLog['field_or_relation'] == 'locationRelation') {
                    $releaseActivityLog['field_or_relation'] = 'location';  
                }
                $fieldOrRelation = implode(array_map('strtolower', preg_split('/(?=[A-Z])/', str_replace("_"," ",$releaseActivityLog['field_or_relation']))));
                $activity = implode('',[$activity, 'set ',$fieldOrRelation," as <{$tag}>{$releaseActivityLog['final_value']}</{$tag}>, "]);
            }
        }
    }

    /** 
     * method to format releases attachment activity
     * @param array $releaseActivityLog
     * @param $activity
     * @return null
     */
    private function formatReleaseAttachmentActvityLog($releaseActivityLog, &$activity)
    {   
        $fieldOrRelation = implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $releaseActivityLog['field_or_relation'])));
        $activity = implode('',[$activity, $releaseActivityLog['event_name'],' ', $fieldOrRelation]);
    }

    /** 
     * method to format popup activity
     * @param array $releaseActivityLogs
     * @param $activity
     * @return null
     */
    private function formatPopUpActvityLog(array &$releaseActivityLog, string &$activity)
    {   
        $added = 'added';
        if( $releaseActivityLog['event_name'] == 'updated') {
            $added = 'updated';
        }
        if(($releaseActivityLog['event_name'] == 'created' or $releaseActivityLog['event_name'] == 'updated') and $releaseActivityLog['log_name'] == 'sd_releases:popup_update' and ($releaseActivityLog['field_or_relation'] == 'build-plan' or $releaseActivityLog['field_or_relation'] == 'test-plan'))
        {   
            $fieldOrRelation = implode(' ', array_map('ucfirst', preg_split('/(?=[A-Z])/', $releaseActivityLog['field_or_relation'])));
            $activity = implode('',[$activity ,' ',$added, ' the Release ', $fieldOrRelation, " "]);
        }   
        elseif($releaseActivityLog['event_name'] == 'deleted' and $releaseActivityLog['log_name'] == 'sd_releases:popup_update' and ($releaseActivityLog['field_or_relation'] == 'build-plan' or $releaseActivityLog['field_or_relation'] == 'test-plan'))
        {   
            $fieldOrRelation = implode(' ', array_map('ucfirst', preg_split('/(?=[A-Z])/', $releaseActivityLog['field_or_relation'])));
            $activity = implode('', [$activity, 'deleted the Release ',$fieldOrRelation, " "]);
        }
    }
    /**
     * method to format release pivot activity log
     * @param Activity $releaseActivityLogs
     * @param array $releaseActivity
     * @return null
     */
    private function formatReleasePivotActivityLog($releaseActivityLogs, array &$releaseActivity)
    {
        switch($releaseActivityLogs[0]['field_or_relation'])
        {
            case 'attachChanges':
                return $this->formatChangeAttachedToReleaseActivityLog($releaseActivityLogs, $releaseActivity);
            case 'attachAssets':
                return $this->formatAssetsAttachedToReleaseActivityLog($releaseActivityLogs, $releaseActivity);
            default:
                return false;
        }
    }

    /**
     * method to format change attached to release activity log
     * @param Activity $releaseActivityLogs
     * @param array $releaseActivity
     * @return null
     */
    private function formatChangeAttachedToReleaseActivityLog($releaseActivityLogs, array &$releaseActivity)
    {
        $changeNames = '';
        foreach ($releaseActivityLogs as $releaseActivityLog) {
            $change = SdChanges::find($releaseActivityLog['final_value']);
            $changeNames = $changeNames . '<a href='.faveoUrl("service-desk/changes/{$change->id}/show").'>'."<b>(#CHN-{$change->id}) {$change->name}</b></a>, ";
        }
        $changeNames = rtrim($changeNames, ', ');
        $activity = "{$releaseActivityLogs[0]['event_name']} change $changeNames";
        $releaseActivity[] = ['id' => $releaseActivityLogs[0]['id'], 'creator' => $releaseActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $releaseActivityLogs[0]['created_at']];


    }

    /**
     * method to format assets attached to release activity log
     * @param Activity $releaseActivityLogs
     * @param array $releaseActivity
     * @return null
     */
    private function formatAssetsAttachedToReleaseActivityLog($releaseActivityLogs, array &$releaseActivity)
    {
        $assetNames = '';
        foreach ($releaseActivityLogs as $releaseActivityLog) {
            $asset = SdAssets::find($releaseActivityLog['final_value']);
            $assetNames = $assetNames . '<a href='.faveoUrl("service-desk/assets/{$asset->id}/show").'>'."<b>(#AST-{$asset->id}) {$asset->name}</b></a>, ";
        }
        $assetNames = rtrim($assetNames, ', ');
        $activity = "{$releaseActivityLogs[0]['event_name']} asset $assetNames";
        $releaseActivity[] = ['id' => $releaseActivityLogs[0]['id'], 'creator' => $releaseActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $releaseActivityLogs[0]['created_at']];
    }

    /**
     * method to get release planning Popup details
     * @param $releaseId
     * @return response
     */
    public function planningPopups($releaseId)
    {
        $owner = "sd_releases:{$releaseId}";
        $planningPopups = GeneralInfo::where('owner',$owner)->get()->toArray();
        $storageControllerInstance = (new StorageController);

        foreach ($planningPopups as &$planningPopup) 
        {   
            $planningPopup['description'] = $planningPopup['value'];
            $planningPopup['attachment'] = Attachments::where('owner', "sd_releases:{$planningPopup['key']}:{$releaseId}")->first();
            if($planningPopup['attachment'])
            {   
                $planningPopup['attachment']['size'] = getSize( $planningPopup['attachment']['size']);
                $planningPopup['attachment']['link'] = implode('', ['/service-desk/download/', $planningPopup['attachment']['id'],'/', $planningPopup['attachment']['owner'], '/attachment']);
                $planningPopup['attachment']['icon_url'] = $storageControllerInstance->getThumbnailUrlByMimeType($planningPopup['attachment']['link']);
            }
            unset($planningPopup['value']);
        }

        return successResponse('', ['planning_popups' => $planningPopups]);
    }

    /** 
     * method to append attachments in release data
     * @param SdReleases $release
     * @return null
     */
    private function appendAttachmentsInRelease(SdReleases $release)
    {   
        $releaseId = $release->id;
        $storageControllerInstance = (new StorageController);
        $release->attachment = Attachments::where('owner', 'sd_releases:' . $releaseId)->get()->transform(function($attachment) use($storageControllerInstance, $releaseId) 
        {
            $attachment->size = getSize($attachment->size);
            $attachment->link = implode('', ['/service-desk/download/', $attachment->id, '/sd_releases:', $releaseId, '/attachment']);
            $attachment->icon_url = $storageControllerInstance->getThumbnailUrlByMimeType($attachment->link);

            return $attachment;
        });
    }

    /**
     * method for release create blade page
     * @return view
     */
    public function releaseCreatePage()
    {
        if (!$this->agentPermission->releaseCreate()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::releases.create');
    }

    /**
     * method for release edit blade page
     * @param $releaseId
     * @return view
     */
    public function releaseEditPage($releaseId)
    {
        if (!$this->agentPermission->releaseEdit()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::releases.edit', compact('releaseId'));
    }

    /**
     * method to delete release
     * @param SdReleases $release
     * @return Response
     */
    public function deleteRelease(SdReleases $release)
    {
        $release->delete();

        return successResponse(trans('ServiceDesk::lang.release_deleted_successfully'));
    }

    /**
     * method to release index page
     * @return view
     */
    public function releaseIndexPage()
    {
        if (!$this->agentPermission->releasesView()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::releases.index');

    }


}