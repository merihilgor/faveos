<?php

namespace App\Plugins\ServiceDesk\Controllers\Cab;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use Request;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflow;

/**
 * ApiCabController is mainly dependent on ApiApprovalWorkflowController only type changes to cab
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiCabController extends BaseServiceDeskController
{
    public function __construct()
    {
        $this->middleware('role.admin');
    }

    /**
     * method to populate approval workflow type to cab
     * @return 
     */
    public function changeTypeToCab(&$type)
    {
        if (strpos(Request::url(), 'service-desk/api/cab') != false) {
            $type = 'cab';
        }
    }
    
    /**
     * method to create cab blade page
     * @return view
     */
    public function create() 
    {
        return view('service::cab.create');
    }

    /**
     * method to edit cab blade page
     * @return view
     */
    public function edit() 
    {
        return view('service::cab.edit');
    }

    /**
     * method to cab index blade page
     * @return view
     */
    public function index() 
    {
        return view('service::cab.index');
    }

    /**
     * Function to change approve and deny status
     * @param array $data
     * @return null
     */
    public function changeApproveDenyStatus(array &$data)
    {
        if (strpos(Request::url(), 'service-desk/api/cab') != false) {
            $cab = SdApprovalWorkflow::find($data['id']);
            $data['action_on_approve'] = $cab->actionOnApprove()->first();
            $data['action_on_deny']    = $cab->actionOnDeny()->first();
        }
    }

}
