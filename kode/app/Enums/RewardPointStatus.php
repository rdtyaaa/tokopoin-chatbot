<?php
  
namespace App\Enums;

use Illuminate\Support\Arr;

enum RewardPointStatus :int {

    use EnumTrait;

    case PENDING        = 1;
    case REDEEMED       = 2;
    case EXPIRED        = 3;

}