<?php

namespace App\Plugins\ServiceDesk\Model\Common;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Common\Email;


class EmailSources extends Model
{ 
	protected $table = 'sd_emails_sources';
    protected $fillable = ['email_id','source_id','source_type'];
}
