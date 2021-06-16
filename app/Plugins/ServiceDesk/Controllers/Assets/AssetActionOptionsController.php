<?php
namespace App\Plugins\ServiceDesk\Controllers\Assets;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;

/**
 * Handles all the actions or action-related data for a user, while handling a change
 *
 */
class AssetActionOptionsController extends BaseServiceDeskController
{
    // agent permission based on logged in user
    protected $agentPermissionPolicy;
    // store current asset details
    protected $asset;

    public function __construct()
    {
        $this->middleware('role.agent');
        $this->agentPermissionPolicy = new AgentPermissionPolicy();
    }

    /**
     * Gets list of actions as allowed(true) or not-allowed(false) for logged in user. 
     * for eg. if the logged in user is not allowed to edit asset
     * @param $assetId
     * @return Response array       success response with array of permissions
     */
    public function getAssetActionList(SdAssets $asset)
    {
        $this->asset = $asset;
        $allowedActions = [
            'attach_problems' => $this->isAssetServicePermissionCheck('problemAttach'),
            'attach_changes' => $this->isAssetServicePermissionCheck('changeAttach'),
            'attach_releases' => $this->isAssetServicePermissionCheck('releaseAttach'),
            // no permission exists for attach contracts and attach tickets, as of now
            // we can think of adding it in future, then code could be written for that
            'attach_contracts' => true,
            'attach_tickets' => true,
            'edit_asset' => $this->isAssetServicePermissionCheck('assetEdit'),
            'delete_asset' => $this->isAssetServicePermissionCheck('assetDelete'),
            'associated_problems_tab_viewable' => $this->isAssetAssoicatedServiceTabViewable('problems'),
            'associated_changes_tab_viewable' => $this->isAssetAssoicatedServiceTabViewable('changes'),
            'associated_releases_tab_viewable' => $this->isAssetAssoicatedServiceTabViewable('releases'),
            'associated_contracts_tab_viewable'  => $this->isAssetAssoicatedServiceTabViewable('contracts'),
            'associated_tickets_tab_viewable' => $this->isAssetAssoicatedServiceTabViewable('tickets'),
            'asset_type_properties' => $this->isExtraPropertiesBlockViewable('customFieldValuesForAssetType')
        ];

        return successResponse('', ['asset_actions' => $allowedActions]);
    }

    /**
     * method to check logged in agent has associated permission or not
     * @param  String $serviceName
     * @return boolean
     */
    private function isAssetServicePermissionCheck(String $serviceName)
    {
        return (bool) $this->agentPermissionPolicy->$serviceName();
    }

    /**
     * method to check associates services has any entry attached or not to existing asset
     * @param  String $serviceName
     * @return boolean
     */
    private function isAssetAssoicatedServiceTabViewable(String $serviceName)
    {
        return (bool) $this->asset->$serviceName()->count();
    }

    /**
     * method to check extra asset properties exist or not
     * @param  String $extraPropertyRelationName
     * @return boolean
     */
    private function isExtraPropertiesBlockViewable(String $extraPropertyRelationName)
    {
        return (bool) $this->asset->$extraPropertyRelationName()->count();
    }
}
