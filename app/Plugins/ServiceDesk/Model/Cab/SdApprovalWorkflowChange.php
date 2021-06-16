<?php

namespace App\Plugins\ServiceDesk\Model\Cab;

use App\Plugins\ServiceDesk\Model\Cab\SdApprovalLevelStatus;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Workflow\ApprovalWorkflowTicket;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class SdApprovalWorkflowChange extends Model
{
    use Observable;

    protected $table = 'sd_approval_workflow_changes';

    protected $fillable = ['approval_workflow_id', 'change_id', 'status', 'name', 'user_id', 'action_on_approve', 'action_on_deny', 'change_status_id'];

    /**
     * Relation with approval level
     */
    public function approvalLevels()
    {
        return $this->hasMany(SdApprovalLevelStatus::class, 'approval_workflow_change_id');
    }

    public function change(){
      return $this->belongsTo(SdChanges::class,'change_id');
    }

     /** 
     * method to delete approvalLevels relation
     */
    public function beforeDelete($model)
    {
      foreach ($model->approvalLevels()->get() as $approverLevel)
      {
        $approverLevel->delete();
      }
    }
}
