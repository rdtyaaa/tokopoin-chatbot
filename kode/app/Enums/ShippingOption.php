<?php

namespace App\Enums;

enum ShippingOption {

    case PRODUCT_CENTRIC;
    case FLAT;
    case SELLER_DEFINED;
    case LOCATION_BASED;
    case CARRIER_SPECIFIC;

    /**
     * Get enum values
     */
    public function getValues(): string
    {
        return match($this) {
            self::PRODUCT_CENTRIC   => 'Product-Based Shipping Fee',
            self::FLAT              => 'Flat Shipping Fee',
            self::LOCATION_BASED    => 'Location Based Shipping Fee',
            self::CARRIER_SPECIFIC  => 'Carrier-Based Shipping Fee',
        };
    }

    public static function toArray() :array{
        return [
            'PRODUCT_CENTRIC'  => (ShippingOption::PRODUCT_CENTRIC)->getValues(),
            'FLAT'             => (ShippingOption::FLAT)->getValues(),
            'LOCATION_BASED'   => (ShippingOption::LOCATION_BASED)->getValues(),
            'CARRIER_SPECIFIC' => (ShippingOption::CARRIER_SPECIFIC)->getValues(),
        ];
    }

}


