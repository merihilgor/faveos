<?php

namespace App\Plugins\ServiceDesk\Controllers\Permission;

// controller
use App\Http\Controllers\Controller;

// classes
use Illuminate\Http\Request;
use DB;
use Exception;
use App\User;
use App\Plugins\ServiceDesk\Model\Permission\AgentPermission;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;


/**
 * PermissionController
 * This controller is used servicedesk agent permissions
 *
 * @author danis.john@ladybirdweb.com Ladybird <info@ladybirdweb.com>
 */
class PermissionController extends Controller {

    //common function for create and update agent permissions
    public function handleCreateUpdate($agent, $user) {

        $agent->sdPermission()->delete();
        if (array_key_exists('sd_permission_ids', $user)) {
            if (!empty($user['sd_permission_ids'])) {
                $sdPermissions = [];
                foreach ($user['sd_permission_ids'] as $key => $value) {
                    $sdPermissions[$value] = "1";
                }
                $agent->sdPermission()->create(['user_id' => $agent->id, 'permission' => $sdPermissions]);
            }
        }
    }
    
    /**
    * edit agent
    * @param $id
    * @return Response
    */    
    public function editAgent($id, &$agent)
    { 
        $sdPermission = AgentPermission::where('user_id', $id)->select('permission')->get()->first();
           if($sdPermission != null){
                $sdPermission = $sdPermission->permission;
                    foreach ($sdPermission as $key => $value) {
                        $permission['sd_permissions'][]  = [ 'id' => $key, 'name' => $this->formatPermission($key)];
                        $agent = (array_merge($agent, $permission));
                    }
            } 
    }
        

    /**
    * Function to format permission
    * @param $permission id
    * @return $permission name
    */
    private function formatPermission($permissionId)
    {
        $permissions = [
                
                ['id' => 'create_problem', 'name' => 'Create Problem'],
                ['id' => 'edit_problem', 'name' => 'Edit Problem'],
                ['id' => 'view_problems', 'name' => 'View Problems'],
                ['id' => 'delete_problem', 'name' => 'Delete Problem'],
                ['id' => 'attach_problem', 'name' => 'Attach Problem'],
                ['id' => 'detach_problem', 'name' => 'Detach Problem'],

                ['id' => 'create_change', 'name' => 'Create Change'],
                ['id' => 'edit_change', 'name' => 'Edit Change'],
                ['id' => 'view_changes', 'name' => 'View Changes'],
                ['id' => 'delete_change', 'name' => 'Delete Change'],
                ['id' => 'attach_change', 'name' => 'Attach Change'],
                ['id' => 'detach_change', 'name' => 'Detach Change'],

                ['id' => 'create_release', 'name' => 'Create Release'],
                ['id' => 'edit_release', 'name' => 'Edit Release'],
                ['id' => 'view_releases', 'name' => 'View Releases'],
                ['id' => 'delete_release', 'name' => 'Delete Release'],
                ['id' => 'attach_release', 'name' => 'Attach Release'],
                ['id' => 'detach_release', 'name' => 'Detach Release'],

                ['id' => 'create_asset', 'name' => 'Create Asset'],
                ['id' => 'edit_asset', 'name' => 'Edit Asset'],
                ['id' => 'view_assets', 'name' => 'View Assets'],
                ['id' => 'delete_asset', 'name' => 'Delete Asset'],
                ['id' => 'attach_asset', 'name' => 'Attach Asset'],
                ['id' => 'detach_asset', 'name' => 'Detach Asset'],

                ['id' => 'create_contract', 'name' => 'Create Contract'],
                ['id' => 'edit_contract', 'name' => 'Edit Contract'],
                ['id' => 'view_contracts', 'name' => 'View Contracts'],
                ['id' => 'delete_contract', 'name' => 'Delete Contract'],


            ];
        foreach ($permissions as $permission) {
            if ($permission['id'] == $permissionId) {
                return $permission['name'];
            }
        } 
    }


     /**
    * gives array of Permission
    * @return array            array of permissions
    */
    protected function permissions(){
        try{
            $permissions = [
                
                ['id' => 'create_problem', 'name' => 'Create Problem'],
                ['id' => 'edit_problem', 'name' => 'Edit Problem'],
                ['id' => 'view_problems', 'name' => 'View Problems'],
                ['id' => 'delete_problem', 'name' => 'Delete Problem'],
                ['id' => 'attach_problem', 'name' => 'attach_problem'],
                ['id' => 'detach_problem', 'name' => 'detach Problem'],

                ['id' => 'create_change', 'name' => 'Create Change'],
                ['id' => 'edit_change', 'name' => 'Edit Change'],
                ['id' => 'view_changes', 'name' => 'View Changes'],
                ['id' => 'delete_change', 'name' => 'Delete Change'],
                ['id' => 'attach_change', 'name' => 'Attach Change'],
                ['id' => 'detach_change', 'name' => 'Detach Change'],

                ['id' => 'create_release', 'name' => 'Create Release'],
                ['id' => 'edit_release', 'name' => 'Edit Release'],
                ['id' => 'view_releases', 'name' => 'View Releases'],
                ['id' => 'delete_release', 'name' => 'Delete Release'],
                ['id' => 'attach_release', 'name' => 'Attach Release'],
                ['id' => 'detach_release', 'name' => 'Detach Release'],

                ['id' => 'create_asset', 'name' => 'Create Asset'],
                ['id' => 'edit_asset', 'name' => 'Edit Asset'],
                ['id' => 'view_assets', 'name' => 'View Assets'],
                ['id' => 'delete_asset', 'name' => 'Delete Asset'],
                ['id' => 'attach_asset', 'name' => 'Attach Asset'],
                ['id' => 'detach_asset', 'name' => 'Detach Asset'],

                ['id' => 'create_contract', 'name' => 'Create Contract'],
                ['id' => 'edit_contract', 'name' => 'Edit Contract'],
                ['id' => 'view_contracts', 'name' => 'View Contracts'],
                ['id' => 'delete_contract', 'name' => 'Delete Contract'],
                
            ];
            return ['permissions' => $permissions];

        }catch(\Exception $e){
            return exceptionResponse($e);
        }
    }


    /**
     * Gets list of actions as allowed(true) or not-allowed(false) for logged in agent 
     * @return Response array       success response with array of permissions
     */
    public function getActionList()
    {
        $agentPermission = new AgentPermissionPolicy();

        $allowedActions = [

            'create_problem' => $agentPermission->problemCreate(),
            'edit_problem' => $agentPermission->problemEdit(),
            'view_problems' => $agentPermission->problemsView(),
            'delete_problem' => $agentPermission->problemDelete(),
            'attach_problem' => $agentPermission->problemAttach(),
            'detach_problem' => $agentPermission->problemDetach(),
             
            'create_change' => $agentPermission->changeCreate(),
            'edit_change' => $agentPermission->changeEdit(),
            'view_change' => $agentPermission->changesView(),
            'delete_change' => $agentPermission->changeDelete(),
            'attach_change' => $agentPermission->changeAttach(),
            'detach_change' => $agentPermission->changeDetach(),

            'create_release' => $agentPermission->releaseCreate(),
            'edit_release' => $agentPermission->releaseEdit(),
            'view_releases' => $agentPermission->releasesView(),
            'delete_release' => $agentPermission->releaseDelete(),
            'attach_release' => $agentPermission->releaseAttach(),
            'detach_release' => $agentPermission->releaseDetach(),

            'create_asset' => $agentPermission->assetCreate(),
            'edit_asset' => $agentPermission->assetEdit(),
            'view_assets' => $agentPermission->assetsView(),
            'delete_asset' => $agentPermission->assetDelete(),
            'attach_asset' => $agentPermission->assetAttach(),
            'detach_asset' => $agentPermission->assetDetach(),

            'create_contract' => $agentPermission->contractCreate(),
            'edit_contract' => $agentPermission->contractEdit(),
            'view_contracts' => $agentPermission->contractsView(),
            'delete_contract' => $agentPermission->contractDelete(),

        ];

        return successResponse('', ['actions' => $allowedActions]);
    }


}
