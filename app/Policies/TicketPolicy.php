<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Auth;

class TicketPolicy {

    /**
     * User whose permission is required
     * @var User
     */
    private $user;

    public function __construct($userId = null)
    {
        if($userId){
            $this->user = User::find($userId);
        }
    }

    use HandlesAuthorization;

    /**
     * check the permission for provided keys
     * @param string $key
     * @return boolean
     */
    public function checkPermission($key) {

        $user = $this->user ? : Auth::user();

        // NOTE FROM AVINASH: returning true because earlier it used to return true if user not logged in. Not sure why
        if(!$user){
            return true;
        }

        // NOTE FROM AVINASH: returning true because earlier it used to return true if user role is user. Not sure why
        if($user->role == "user" || $user->role == "admin"){
            return true;
        }

        if ($user->permision()->first()) {
            $permission = $user->permision()->first()->permision;
            $isPermitted = (is_array($permission) && checkArray($key, $permission));
            return (bool)$isPermitted;
        }

        return false;
    }

    /**
     * ticket create permission
     * @return boolean
     */
    public function create() {
        return $this->checkPermission('create_ticket');
    }

    /**
     * ticket edit permission
     * @return boolean
     */
    public function edit() {
        return $this->checkPermission('edit_ticket');
    }

    /**
     * ticket close permission
     * @return boolean
     */
    public function close() {
        return $this->checkPermission('close_ticket');
    }

    /**
     * ticket assign permission
     * @return boolean
     */
    public function assign() {
        return $this->checkPermission('assign_ticket');
    }

    /**
     * ticket transfer/change department permission
     * @return boolean
     */
    public function transfer() {
        return $this->checkPermission('transfer_ticket');
    }

    /**
     * ticket delete permission
     * @return boolean
     */
    public function delete() {
        return $this->checkPermission('delete_ticket');
    }

    /**
     * ban user permission
     * @return boolean
     */
    public function ban() {
        return $this->checkPermission('ban_email');
    }

    /**
     * access kb permission
     * @return boolean
     */
    public function kb() {
        return $this->checkPermission('access_kb');
    }

    public function orgUploadDoc() {
        return $this->checkPermission('organisation_document_upload');
    }

    /**
     * access report permission
     * @return boolean
     */
    public function report() {
        return $this->checkPermission('report');
    }

    /**
     * verify email permission
     * @return boolean
     */
    public function emailVerification() {
        return $this->checkPermission('email_verification');
    }

    /**
     * verify mobile permission
     * @return boolean
     */
    public function mobileVerification() {
        return $this->checkPermission('mobile_verification');
    }

    public function ChangePriority() {
        return $this->checkPermission('change_priority');
    }

    /**
     * activate account permission
     * @return boolean
     */
    public function accountActivation() {
        return $this->checkPermission('account_activate');
    }

    /**
     * activate agent account permission
     * @return boolean
     */
    public function agentAccountActivation() {
        return $this->checkPermission('agent_account_activate');
    }

    public function changeDuedate() {
        return $this->checkPermission('change_duedate');
    }

    public function globalAccess() {
        return $this->checkPermission('global_access');
    }

    public function reAssigningTickets() {
        return $this->checkPermission('re_assigning_tickets');
    }

    public function restrictedAccess() {
        return $this->checkPermission('restricted_access');
    }

    /**
     * Permission whether logged in user can view un-approved tickets
     * @return boolean
     */
    public function viewUnapprovedTickets(){
        return $this->checkPermission('view_unapproved_tickets');
    }


    /**
     * Approval workflow permission
     * @return boolean
     */
    public function applyApprovalWorkflow(){
        return $this->checkPermission('apply_approval_workflow');
    }


    /**
     * User profile Access permission
     * @return boolean
     */
    public function accessUserProfile(){
        return $this->checkPermission('access_user_profile');
    }



    /**
     * Organization profile access permission
     * @return boolean
     */
    public function accessOrganizationProfile(){
        return $this->checkPermission('access_organization_profile');
    }

    /**
     * Recur ticket access permission
     * @return boolean
     */
    public function accessRecurTicket(){
        return $this->checkPermission('recur_ticket');
    }
}
