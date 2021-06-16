<?php

namespace App\Plugins\ServiceDesk\Controllers\Announcement;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Request\Announcement\CreateUpdateAnnouncementRequest;
use App\Plugins\ServiceDesk\Model\Common\SdOrganization;
use App\Plugins\ServiceDesk\Model\Common\SdDepartment;
use App\Http\Controllers\Common\PhpMailController;

/**
 * Handles API's for Announcement Controller
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiAnnouncementController extends BaseServiceDeskController
{
    public function __construct() {
        $this->middleware('role.admin');
    }
    
    /** 
     * method for announce blade page
     * @return view
     */
    public function announcementPage()
    {
        return view('service::announce.set');
    }

    /**
     * method to make announcement
     * @param CreateUpdateAnnouncedRequest $request
     * @return Response
     */
    public function makeAnnouncement(CreateUpdateAnnouncementRequest $request)
    {
        $announcementData = $request->toArray();
        $errorResponse = '';
        ($announcementData['option'] == 'organization_id') ? $this->sendOrganization($announcementData['organization_id'], $announcementData['subject'], $announcementData['announcement'], $errorResponse) : $this->sendDepartment($announcementData['department_id'], $announcementData['subject'], $announcementData['announcement'], $errorResponse);
        if ($errorResponse) {
            return errorResponse($errorResponse);
        }

        return successResponse(trans('ServiceDesk::lang.announced'));
    }

    /** 
     * method to send announcement to organization
     * @param integer $organizationId
     * @param string $subject
     * @param string $message
     * @param string $errorResponse
     * @return null
     */
    private function sendOrganization($organizationId, $subject, $message, &$errorResponse)
    {
        $organization = SdOrganization::find($organizationId);

        if (is_null($organization)) {
            $errorResponse = trans('lang.organization_not_found');
            return null;
        }

        $organizationMembers = $organization->members();
        $this->sendAnnouncementToMembers($organizationMembers, $subject, $message, $errorResponse);
    }

    /** 
     * method to send announcement to department members
     * @param integer $departmentId
     * @param string $subject
     * @param string $message
     * @param string $errorResponse
     * @return null
     */
    private function sendDepartment($departmentId, $subject, $message, &$errorResponse)
    {
        $department = SdDepartment::find($departmentId);

         if (is_null($department)) {
            $errorResponse = trans('lang.department_not_found');
            return null;
        }

        $departmentMembers = $department->agents();
        $this->sendAnnouncementToMembers($departmentMembers, $subject, $message, $errorResponse);
    }

    /** 
     * method to send announcement (email)  to organization / department members
     * @param User $members
     * @param string $subject
     * @param string $message
     * @param string $errorResponse
     * @return null
     */
    private function sendAnnouncementToMembers($members, $subject, $message, &$errorResponse)
    {
        if ($members->get()->isEmpty()) {
            $errorResponse = trans('lang.no_members');
            return null;
        }
        $templateVariables = [];
        $members = $members->select('first_name', 'last_name', 'email')->get();
        $phpMailController = new PhpMailController();
        $from = $phpMailController->mailfrom('1', '0');
        $message = [
            'subject' =>$subject,
            'scenario' => null,
            'body' => $message
        ];
        foreach ($members as $member) {
            $to = [
                'name' => $member->full_name,
                'email' => $member->email,
            ];
            $phpMailController->sendmail($from, $to, $message, $templateVariables);
        }
    }

}
