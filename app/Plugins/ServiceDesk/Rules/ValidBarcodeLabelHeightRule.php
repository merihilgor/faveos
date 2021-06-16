<?php

namespace App\Plugins\ServiceDesk\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidBarcodeLabelHeightRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value >= 0.8346457 && $value <= 11;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('ServiceDesk::lang.label_height_valid');
    }
}
