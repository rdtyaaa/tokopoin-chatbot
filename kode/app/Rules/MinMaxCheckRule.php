<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MinMaxCheckRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $range;
    public function __construct($range)
    {
        $this->range = $range;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(in_array($value,$this->range)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return translate('Maximum Vlaue Can Not Be Greter Than ').$this->range[1];
    }
}
