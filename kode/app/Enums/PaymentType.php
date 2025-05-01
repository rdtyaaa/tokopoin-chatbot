<?php
  
namespace App\Enums;

use Illuminate\Support\Arr;

enum PaymentType :int {

    use EnumTrait;


    case ORDER        = 0;
    case WALLET       = 1;




}