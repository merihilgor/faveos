<?php

namespace App\Plugins\ServiceDesk\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSpaceBetweenLabelsRule implements Rule
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
        return $value >= 0 && $value <= 10;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('ServiceDesk::lang.space_between_labels_valid');
    }
}
