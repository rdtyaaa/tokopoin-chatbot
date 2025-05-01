<?php

namespace App\Enums\Settings;

use App\Enums\EnumTrait;

enum NotificationType: string
{
    use EnumTrait;

    case ORDER                      = "order";
    case DELIVERYMAN_CHAT           = "deliveryman_chat";
    case SELF_DELIVERYMAN_CHAT      = "self_deliveryman_chat";
    case CUSTOMER_CHAT              = "customer_chat";
    case SELLER_CHAT                = "seller_chat";
    case PRODUCT_UPDATE             = "product_update";
 
    
}