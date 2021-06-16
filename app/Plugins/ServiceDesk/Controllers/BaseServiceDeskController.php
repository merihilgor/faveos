<?php
namespace App\Plugins\ServiceDesk\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use DateTime;
use App\Traits\FaveoDateParser;

class BaseServiceDeskController extends Controller{
    use FaveoDateParser;
    
    public function __construct() {
        \Event::dispatch('service.desk.activate', array());
    }


    /** 
     * method to search user
     * @param QueryBuilder $baseQuery
     * @param string $relationName // associated user relation name
     * @param string $searchString
     * @return null
     */
    protected function userSearchQuery(QueryBuilder &$baseQuery, string $relationName, string $searchString)
    {
        $baseQuery = $baseQuery->orWhereHas($relationName, function($query) use($searchString) {
                    $query
                          ->where('user_name', 'LIKE', "%$searchString%")
                          ->orWhere('email', 'LIKE', "%$searchString%")
                          ->orWhereRaw("concat(first_name, ' ', last_name) LIKE ?", ['%'.$searchString.'%']);
                });
    }

    /**
     * method to make empty attributes as nullable
     * only for attributes which has id in their name
     * @param array $attributes
     * @return null
     */
    protected function  makeEmptyAttributesNullable(array &$attributes)
    {
        foreach ($attributes as $attribute) {
            $attribute = (!is_array($attribute) && strpos($attribute, '_id') !== false) ? NULL : $attribute;
        }
    }

    /**
     * method to split date and time from timestamp 
     * @param  $timeStamp (timestamp includes date and time)
     * @param array $timeStamp (array key set with date and time)
     * @return null
     */
    protected function splitDateAndTimeFromTimeStamp($timeStamp)
    {   
        $splittedDateAndTime = [];
        $splittedDateAndTime['date'] = date('Y-m-d',strtotime($timeStamp));
        $splittedDateAndTime['time'] = date('H:i:s',strtotime($timeStamp));

        return $splittedDateAndTime;
    }

    /**
     * method to count number of days
     * @param $startingTimeStamp (timestamp with which it need to be compared)
     * @return integer $numberOfDays
     */
    protected function countNumberOfDays($startingTimeStamp)
    {
        $todayDateInUtc = new DateTime(date("Y-m-d H:i:s"));
        $startingTimeStamp = new DateTime($startingTimeStamp);
        $numberOfDays = $startingTimeStamp->diff($todayDateInUtc)->days;

        return $numberOfDays;

    }
}
