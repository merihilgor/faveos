<?php

namespace App\Plugins\ServiceDesk\Traits;

use DB;

/**
 * Handles identifier generation logic
 */
trait IdentifierHandler
{

  /**
   * Generate Identifier
   * If identifier is duplicated then 
   * Eg: PRB-2, PRB-2(1), PRB-2(2)
   * @param $modelObject
   * @param $tag
   * @param $modelClass
   * @return null
   */
  private function generateIdentifier($modelObject, $tag, $modelClass)
  {
    if (!$modelObject->identifier) {
      $identifier = implode('', [$tag, $modelObject->id]);
      $numberOfTimesIdentifierDuplicatedCount = $modelClass::where('identifier' ,'LIKE',"%{$identifier}%")->count();
      if ($numberOfTimesIdentifierDuplicatedCount) {
        $identifier = implode('', [$identifier, "({$numberOfTimesIdentifierDuplicatedCount})"]);
      }
      $modelObject->identifier = $identifier;
      $modelClass::updateOrCreate(['id' => $modelObject->id], $modelObject->toArray());
      // if condition could be removed once activity log for contract, release gets merged in development branch
      if (method_exists($modelObject, 'activityLog')) {
        // deleting extra empty identifier activity log generated after create or update
        $modelObject->activityLog()->where([['field_or_relation', 'identifier'], ['final_value', '']])->delete();
      }
    }
  }

}
