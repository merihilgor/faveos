<?php

namespace App\Plugins\ServiceDesk\Model\Cab;

use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use App\Plugins\ServiceDesk\Model\Cab\SdApproverStatus;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use App\Model\helpdesk\Manage\UserType;
use App\User;

class SdApprovalLevelStatus extends ApprovalLevelStatus
{

    protected $table = 'sd_approval_level_statuses';

    protected $fillable = ['approval_level_id', 'approval_workflow_change_id', 'name', 'match',
        'order','is_active','status'];

    /**
     * relation with Approver Statuses
     */
    public function approverStatuses()
    {
      return $this->hasMany(SdApproverStatus::class, 'approval_level_status_id');
    }

     /**
     * Relation with user
     */
    public function approveUsers()
    {
        return $this->morphedByMany(User::class, 'approver','sd_approver_statuses','approval_level_status_id')
          ->withPivot('hash', 'status')->select('users.id','first_name','last_name','user_name','email');
    }

    /**
     * Relation with user types
     */
    public function approveUserTypes()
    {
        return $this->morphedByMany(UserType::class, 'approver','sd_approver_statuses','approval_level_status_id')->withPivot('hash', 'status');
    }

    public function approvalWorkflow()
    {
        return $this->belongsTo(SdApprovalWorkflowChange::class, 'approval_workflow_change_id');
    }

    /**
     * method to delete approverStatus relation
     */
    public function beforeDelete($model)
    {
      foreach ($model->approverStatuses()->get() as $approverStatus) {
          $approverStatus->delete();
      }
    }
}
