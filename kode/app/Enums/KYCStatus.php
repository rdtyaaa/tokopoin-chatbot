<?php
  
namespace App\Enums;

use Illuminate\Support\Arr;

enum KYCStatus :int {

    use EnumTrait;

    case APPROVED       = 1;
    case REQUESTED      = 2;
    case HOLD           = 3;
    case REJECTED       = 4;


}