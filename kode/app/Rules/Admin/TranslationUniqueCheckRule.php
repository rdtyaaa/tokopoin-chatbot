<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;

class TranslationUniqueCheckRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $id,$model,$column,$title,$lang_code;
    public function __construct($model,$column, $id = null ,$lang_code =  null)
    {
        $this->id = $id;
        $this->column = $column;
        $this->model = $model;
;
        $this->lang_code = $lang_code;
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
        $flag = 1;
        $translation_data = app(config('constants.options.model_namespace').$this->model)::latest()->pluck($this->column)->toArray();

   
        $lang_code = $this->lang_code ? $this->lang_code : get_system_locale();
        if($this->id){
            $translation_data  = app(config('constants.options.model_namespace').$this->model)::latest()->where('id','!=',$this->id)->pluck($this->column)->toArray();
        }

        foreach($translation_data as $data){
            if(@json_decode($data,true)[$lang_code] == $value){
                $flag = 0;
                break;
            }
        }

        if($flag == 1){
            return true ;
        }
        else{
            return false ;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return  ('The '.$this->column.' filed must be unique');
    }
}
