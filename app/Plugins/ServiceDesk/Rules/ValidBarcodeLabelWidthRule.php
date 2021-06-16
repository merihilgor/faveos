<?php

namespace App\Plugins\ServiceDesk\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidBarcodeLabelWidthRule implements Rule
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
        return $value >= 1.5 && $value <= 8.5;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('ServiceDesk::lang.label_width_valid');
    }
}
