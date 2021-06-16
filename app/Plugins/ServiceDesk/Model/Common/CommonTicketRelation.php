<?php
namespace App\Plugins\ServiceDesk\Model\Common;
use Illuminate\Database\Eloquent\Model;

class CommonTicketRelation extends Model
{
    protected $table = 'sd_common_ticket_relation';
    protected $fillable = ['ticket_id','type_id','type'];

    /**
    * relationship with ticket
    */
    public function tickets(){
        return $this->hasMany(\App\Model\helpdesk\Ticket\Tickets::class, 'id', 'ticket_id');
    }


    /*
    	asset, problem

    */
    
}