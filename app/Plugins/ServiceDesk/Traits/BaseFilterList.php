<?php

namespace App\Plugins\ServiceDesk\Traits;

/**
 * BaseFilterList Trait to handle all common methods for Filter
 *
 */
trait BaseFilterList
{

     /**
     * method to check for the passed fieldName in request and appends it query to baseQuery from DB
     * NOTE: All datetime passed are assumed to be in agent's timezone
     *
     * @param string $parameterNameInRequest it holds time range data in start and end parameters
     * @param string $fieldNameInDB it holds field name by which we query
     * @param Model Query $baseQuery It is the base query 
     * to which time range where clause has to be appended
     * @return null
     */
    protected function filteredServiceQueryModifierForTimeRange($parameterNameInRequest, $fieldNameInDB, &$baseQuery)
    {
        $timeRange = $this->request->input($parameterNameInRequest);

        if($timeRange){

            $formattedRange = $this->getTimeRangeObject($timeRange, agentTimeZone());
            
            $baseQuery = $baseQuery->where($fieldNameInDB, '<=', $formattedRange->end)->where($fieldNameInDB, '>=', $formattedRange->start);
        }
    }

}
