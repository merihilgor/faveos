<?php 
namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use App\Plugins\ServiceDesk\Rules\ValidBarcodeLabelWidthRule;
use App\Plugins\ServiceDesk\Rules\ValidBarcodeLabelHeightRule;
use App\Plugins\ServiceDesk\Rules\ValidSpaceBetweenLabelsRule;

class BarcodeTemplateRequest extends Request
{
    use RequestJsonValidation;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'width'  => ['required','numeric',new ValidBarcodeLabelWidthRule],
            'height' => ['required','numeric',new ValidBarcodeLabelHeightRule],
            'labels_per_row' => ['required','numeric',new ValidSpaceBetweenLabelsRule],
            'space_between_labels' => ['required','numeric',new ValidSpaceBetweenLabelsRule],
            'display_logo_confirmed' => 'required',
            'logo_image' => 'nullable|image|mimes:jpg,png,jpeg',
            'image_exits' => 'nullable'
            ];

        return $rules;
    }

}