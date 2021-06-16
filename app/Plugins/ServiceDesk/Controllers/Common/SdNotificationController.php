<?php
namespace App\Plugins\ServiceDesk\Controllers\Common;

use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController;
use ReflectionClass;

/**
 * This controller is used to push in app notification
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class SdNotificationController extends NotificationController
{

    /**
     * function to populate content
     * @param string $content
     * @param object $model
     * @param string $key
     * @return 
     */
    public function getSdContent(&$content, $model, $key)
    {
        switch ($key) {
            case 'approved_contract':
                $content = 'Contract : ' .$model->name . ' is approved by ' . $model->approverRelation->name();
                break;
            case 'rejected_contract':
                $content = 'Contract : ' .$model->name . ' is rejected by ' . $model->approverRelation->name();
                break;

            case 'default':
                break;

        }

    }

    /**
     * function to populate url
     * @param string $url
     * @param object $model
     * @return 
     */
    public function getSdUrl(&$url, $model)
    {
        $class = (new ReflectionClass($model))->getShortName();
        switch($class) {
            case 'SdContract':
                $url = faveoUrl('service-desk/contracts/' . $model->id . '/show');
                break;

            case 'default':
                break;
        } 

    }

    
}
