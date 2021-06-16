<?php

namespace App\Plugins\ServiceDesk\Model\Cab;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\Model\helpdesk\Workflow\ApprovalLevel;

class SdApprovalWorkflow extends Model
{
	protected $table = 'approval_workflows';

    /**
     * relation for getting corresponding action status on approve
     */
    public function actionOnApprove()
    {
      return $this->belongsTo(SdChangestatus::class,'action_on_approve')
        ->select('id','name');
    }

    /**
     * relation for getting corresponding action status on deny
     */
    public function actionOnDeny()
    {
      return $this->belongsTo(SdChangestatus::class,'action_on_deny')
        ->select('id','name');
    }

    /**
     * Relation with approval level
     */
    public function approvalLevels()
    {
        return $this->hasMany(ApprovalLevel::class, 'approval_workflow_id');
    }
}
