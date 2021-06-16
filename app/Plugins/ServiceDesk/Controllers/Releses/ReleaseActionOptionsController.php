<?php
namespace App\Plugins\ServiceDesk\Controllers\Releses;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasestatus;

/**
 * Handles all the actions or action-related data for a user, while handling a release
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */
class ReleaseActionOptionsController extends BaseServiceDeskController
{
    // agent permission based on logged in user
    protected $agentPermissionPolicy;

    public function __construct()
    {
        $this->agentPermissionPolicy = new AgentPermissionPolicy();
    }

    /**
     * Gets list of actions as allowed(true) or not-allowed(false) for logged in user. 
     * for eg. if the logged in user is not allowed to edit change
     * @param $release
     * @return Response array       success response with array of permissions
     */
    public function getReleaseActionList(SdReleases $release)
    {
        $allowedActions = [
            'release_edit' => $this->isReleasePermission('releaseEdit'),
            'release_delete' => $this->isReleasePermission('releaseDelete'),
            'release_view' => $this->isReleasePermission('releasesView'),
            'release_change_attach' => $this->isReleasePermission('changeAttach'),
            'release_change_detach' => $this->isReleasePermission('changeDetach'),
            'release_asset_attach' => $this->isReleasePermission('assetAttach'),
            'release_asset_detach' => $this->isReleasePermission('assetDetach'),
            'associated_asset' => $this->checkAssociatedAssets($release),
            'associated_change' => $this->checkAssociatedChange($release),
            'check_planning' => $this->checkplanning($release),
            'release_completed' => $this->isReleaseStatusCompleteable($release),
        ];

        return successResponse('', ['actions' => $allowedActions]);
    } 

    /**
     * method to check agent has edit release permission and for edit release button visibility
     * @param $permission (expected values => 'releaseEdit', 'releaseDelete', 'releasesView', 'changeAttach', 'changeDetach', 'assetAttach', 'assetDetach')
     * @return boolean
     */
    private function isReleasePermission($permission)
    {
        return (bool) $this->agentPermissionPolicy->$permission();
    }

    /**
     * method to check associated assets and for associated assets tab visibility
     * @param $release
     * @return boolean
     */
    private function checkAssociatedAssets($release)
    {
        return (bool) $release->attachAssets()->count();
    }

    /**
     * method to check associated changes and for associated changes tab visibility
     * @param $release
     * @return boolean
     */
    private function checkAssociatedChange($release)
    {
        return (bool) $release->attachChanges()->count();
    }

    /**
     * method to check associated planning data and for associated planning tab visibility
     * @param $release
     * @return boolean
     */
    private function checkplanning($release)
    {
        $releaseModel = implode('',['sd_releases:'.$release->id]);
        return (bool) GeneralInfo::where('owner', $releaseModel)->count();

    }

    /**
     * method to check release status could be completed and for Mark as completed button visibility
     * @param $release
     * @return boolean
     */
    private function isReleaseStatusCompleteable($release)
    {
        if(($closedStatusId = SdReleasestatus::where('name', 'Completed')->first()))
        {
            $closedStatusId = $closedStatusId->id;
        }
        return (bool) SdReleases::where([['id', $release->id],['status_id', '<>', $closedStatusId]])->count();
    }
}
