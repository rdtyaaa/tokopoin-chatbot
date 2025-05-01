<?php

namespace App\Rules\General;

use Illuminate\Contracts\Validation\Rule;

class FileExtentionCheckRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $extention;
    public $type;
    public function __construct($extention,$type = 'image')
    {
       $this->extention = $extention;
       $this->type = $type;;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value){


        $flag = 1;
        if(is_array($value)){
            foreach($value as $file){
                if(!in_array($file->getClientOriginalExtension(), $this->extention)){
                    $flag = 0;
                    break;
                }
            }
        }
        else{
            if(!in_array($value->getClientOriginalExtension(), $this->extention)){
                $flag = 0;
            }
        }
        if( $flag == 1){
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
        return translate($this->type.' Must be '.implode(", ", $this->extention).' Format');
    }
}
