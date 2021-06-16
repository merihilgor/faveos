<?php
namespace App\Plugins\ServiceDesk\Model\FormGroup;

use App\Model\helpdesk\Form\FormGroup as HelpdeskFormGroup;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;

/**
 * FormGroup model implements all the functionality of Helpdesk FormGroup
 * Extra AssetTypes relation is added in this model
 *
 */
class FormGroup extends HelpdeskFormGroup
{
	/**
	 * relation with asset type
	 */
    public function assetTypes() {
		return $this->belongsToMany(SdAssettypes::class, 'sd_asset_type_form_group','form_group_id','asset_type_id');
	}
}
