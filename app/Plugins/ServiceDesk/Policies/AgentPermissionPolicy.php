<?php

namespace App\Plugins\ServiceDesk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Auth;

class AgentPermissionPolicy{

    use HandlesAuthorization;

    /**
     * check the permission for provided keys
     * @param string $key
     * @return boolean
     */

    public function checkAgentPermission($key){
        $check = true;
        if (Auth::check() && (Auth::user()->role == 'admin')) {
            return $check;
        }
        if (Auth::user()->role == 'agent' && Auth::user()->sdPermission()->first()) {
            $sdPermission = Auth::user()->sdPermission()->first()->permission;
            $isPermitted = (is_array($sdPermission) && checkArray($key, $sdPermission));
            $check = $isPermitted ? true : false;
            return $check;
        }         
    }

    
    /**
     * problem create permission
     * @return boolean
     */
    public function problemCreate(){
          return $this->checkAgentPermission('create_problem');
    }

    /**
     * problem edit permission
     * @return boolean
     */
    public function problemEdit(){
          return $this->checkAgentPermission('edit_problem');
    }

    /**
     * problem view permission
     * @return boolean
     */
    public function problemsView(){
         return $this->checkAgentPermission('view_problems');
    }

    /**
     * delete problem
     * @return boolean
     */
    public function problemDelete(){
         return $this->checkAgentPermission('delete_problem');
    }


    /**
     * attach problem
     * @return boolean
     */
    public function problemAttach(){
         return $this->checkAgentPermission('attach_problem');
    }

    /**
     * detach problem
     * @return boolean
     */
    public function problemDetach(){
         return $this->checkAgentPermission('detach_problem');
    }


    /**
     * create change permission
     * @return boolean
     */
    public function changeCreate(){
          return $this->checkAgentPermission('create_change');
    }

    /**
     * edit change permission
     * @return boolean
     */
    public function changeEdit(){
         return $this->checkAgentPermission('edit_change');
    }

    /**
     * change view permission
     * @return boolean
     */
    public function changesView(){
         return $this->checkAgentPermission('view_changes');
    }

    /**
     * change delete permission
     * @return boolean
     */
    public function changeDelete(){
         return $this->checkAgentPermission('delete_change');
    }

    /**
     * attach change
     * @return boolean
     */
    public function changeAttach(){
         return $this->checkAgentPermission('attach_change');
    }

    /**
     * detach change
     * @return boolean
     */
    public function changeDetach(){
         return $this->checkAgentPermission('detach_change');
    }




    /**
     * create release permission
     * @return boolean
     */
    public function releaseCreate(){
          return $this->checkAgentPermission('create_release');
    }

    /**
     * edit release permission
     * @return boolean
     */
    public function releaseEdit(){
           return $this->checkAgentPermission('edit_release');
    }

    /**
     * release view permission
     * @return boolean
     */
    public function releasesView(){
           return $this->checkAgentPermission('view_releases');
    }

    /**
     * release delete permission
     * @return boolean
     */
    public function releaseDelete(){
         return $this->checkAgentPermission('delete_release');
    }

    /**
     * attach release
     * @return boolean
     */
    public function releaseAttach(){
           return $this->checkAgentPermission('attach_release');
    }

    /**
     * detach release
     * @return boolean
     */
    public function releaseDetach(){
         return $this->checkAgentPermission('detach_release');
    }




    /**
     * create asset permission
     * @return boolean
     */
    public function assetCreate(){
          return $this->checkAgentPermission('create_asset');
    }

    /**
     * edit asset permission
     * @return boolean
     */
    public function assetEdit(){
          return $this->checkAgentPermission('edit_asset');
    }

    /**
     * asset view permission
     * @return boolean
     */
    public function assetsView(){
          return $this->checkAgentPermission('view_assets');
    }

    /**
     * asset delete permission
     * @return boolean
     */
    public function assetDelete(){
         return $this->checkAgentPermission('delete_asset');
    }

    /**
     * asset attach permission
     * @return boolean
     */
    public function assetAttach(){
          return $this->checkAgentPermission('attach_asset');
    }

    /**
     * asset detach permission
     * @return boolean
     */
    public function assetDetach(){
         return $this->checkAgentPermission('detach_asset');
    }



    /**
     * create contract permission
     * @return boolean
     */
    public function contractCreate(){
          return $this->checkAgentPermission('create_contract');
    }

    /**
     * edit contract permission
     * @return boolean
     */
    public function contractEdit(){
          return $this->checkAgentPermission('edit_contract');
    }

    /**
     * contract view permission
     * @return boolean
     */
    public function contractsView(){
          return $this->checkAgentPermission('view_contracts');
    }

    /**
     * contract delete permission
     * @return boolean
     */
    public function contractDelete(){
         return $this->checkAgentPermission('delete_contract');
    }

    /**
     * attach contract
     * @return boolean
     */
    public function contractAttach(){
         return $this->checkAgentPermission('attach_contract');
    }

    /**
     * detach contract
     * @return boolean
     */
    public function contractDetach(){
         return $this->checkAgentPermission('detach_contract');
    }


}