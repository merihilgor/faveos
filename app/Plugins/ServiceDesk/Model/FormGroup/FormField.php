<?php

namespace App\Plugins\ServiceDesk\Model\FormGroup;

use Illuminate\Database\Eloquent\Collection;
use App\Model\helpdesk\Form\FormField as HelpdeskFormField;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;

/**
 * FormField model is to provide asset custom field list functionality
 * remaining functionality is handled from Helpdesk FormField Model
 *
 */
class FormField extends HelpdeskFormField {

  /**
   * gets all asset related custom fields including child fields of asset type
   * along with their current label without any meta data
   * NOTE: Attachment fields will not be returning, because it is not required in any of the cases
   * that we are using yet.
   * @return Collection
   */
  public static function getAssetCustomFieldList() : Collection
  {
    $customFields = HelpdeskFormField::where(function($query){
            // category 4 means asset
            $query->where([['category_type','App\Model\helpdesk\Form\FormCategory'], ['category_id', 4]])
              ->where([['default', 0], ['is_active', 1], ['is_filterable', 1], ['type', '!=', 'file']])
              ->orWhereHas('formGroup', function($subQuery) {
                $subQuery->whereGroupType('asset');
              });
          })
          ->get(['id']);

    
    // since all form fields which belongs to asset can be linked to a asset at any given time,
    // so we can consider it to be a normal asset form field
    HelpdeskFormField::appendRecursiveFormFields($customFields);

    return $customFields;
  }

  /**
   * relation to get custom field activity log for particular custom field
   */
  public function customFieldActivityLog()
  {
    return $this->hasMany(Activity::class, 'field_or_relation')->where('log_name', 'asset_custom_field');
  }

}
