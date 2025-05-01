<?php
  
namespace App\Enums;
 
enum StatusEnum {
    case true;
    case false;

    /**
     * get enum status
     */
    public function status(): string
    {
        return match($this) 
        {
            StatusEnum::true   => '1',   
            StatusEnum::false  => '0',   
        };
    }


    
    public static function toArray() :array{
        return [
            'Active'  => (StatusEnum::true)->status(),
            'Inctive' => (StatusEnum::false)->status()
        ];
    }

}