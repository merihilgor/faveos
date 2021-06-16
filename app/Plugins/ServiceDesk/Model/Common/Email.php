<?php

namespace App\Plugins\ServiceDesk\Model\Common;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;


class Email extends Model
{
    protected $table = 'sd_emails';
    protected $fillable = ['email'];
}
