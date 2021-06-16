<?php

namespace App\Plugins\ServiceDesk\Model\Cab;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Model\helpdesk\Workflow\ApproverStatus;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalLevelStatus;

class SdApproverStatus extends ApproverStatus
{
    protected $table = 'sd_approver_statuses';

    protected $fillable = ['approver_id', 'approver_type','approval_level_status_id','status','hash','comment'];

    public function approvalLevelStatus()
    {
      return $this->belongsTo(SdApprovalLevelStatus::class, 'approval_level_status_id');
    }
}
