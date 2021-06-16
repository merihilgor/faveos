<?php

namespace App\Plugins\ServiceDesk\Model\Common;

use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;

class Ticket extends Tickets
{
    public function ticketRelation(){
        return $this->hasMany("App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation","ticket_id");
    }

    public function getTicketRelation($table){

        $relation = $this->ticketRelation()->where('type', $table)->first();
        if($relation){
            $shcema = $table;
            $id = $relation->type_id;
            $model = $this->switchCase($shcema, $id);
            if($model){
                return $model;
            }
        }
    }

    public function switchCase($table,$id){
        $problem = new \App\Plugins\ServiceDesk\Model\Problem\SdProblem();
        $asset = new \App\Plugins\ServiceDesk\Model\Assets\SdAssets();
        $change = new SdChanges();
        switch ($table){
            case "sd_problem":
                return $problem->find($id);
            case "sd_asset":
                return $asset->find($id);
            case "sd_changes":
                return $change->find($id);
        }
    }

    public function table(){
        return $this->table;
    }

    public function subject(){
        $subject = "";
        $thread = $this->thread()->whereNotNull('title')->first();
        $id = $this->attributes['id'];
        if($thread){
            $title = $thread->title;
            $subject = "<a href=".url('thread/'.$id).">".$title."</a>";
        }
        return $subject;
    }

    /**
    * relationship with problem
    */
    public function problems(){
        return $this->belongsToMany(
            'App\Plugins\ServiceDesk\Model\Problem\SdProblem',
            'sd_common_ticket_relation',
            'ticket_id',
            'type_id'
        )->wherePivot('type', 'sd_problem');
    }

    /**
    * relationship with asset
    */
    public function assets(){
        return $this->belongsToMany(
            'App\Plugins\ServiceDesk\Model\Assets\SdAssets',
            'sd_common_ticket_relation',
            'ticket_id',
            'type_id'
        )->wherePivot('type', 'sd_assets');
    }

    /**
     * relationship with change
     */
    public function changes(){
        return $this->belongsToMany(
            SdChanges::class,
            'sd_change_ticket',
            'ticket_id',
            'change_id'
        );
    }

    /**
     * custom field morph class is getting set to servicedesk ticket model
     * so, making it to helpdesk Tickets model
     * getting used for customFieldValues relation of helpdesk Tickets model
     */
    public function getMorphClass()
    {
        return Tickets::class;
    } 


}
