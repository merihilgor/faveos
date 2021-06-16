<?php

namespace App\Plugins\ServiceDesk\Traits;
use Illuminate\Http\Request;

/**
 * Handles updation and deletion of attachment
 */
trait ShallDeleteAttachmentModel
{
    /**
    * Function to delete attachment
    * @param Request $request
    * @param $attachment (either attachement or null)
    * @return boolean
    */
    public function shallDeleteAttachment(Request $request, $attachment)
    {
        if($request->attachment_delete)
        {
            return true;
        }
        if($request->file('attachment') && $attachment)
        {
            return true;
        }
    }
}
