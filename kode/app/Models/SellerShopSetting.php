<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerShopSetting extends Model
{
    use HasFactory;


    protected $fillable = [
        'seller_id',
        'name',
        'email',
        'phone',
        'address',
        'short_details',
        'shop_logo',
        'shop_first_image',
        'shop_second_image',
        'shop_third_image',
        'seller_site_logo',
        'uid'
    ];

    protected static function booted()
    {
        static::creating(function ($sellerShopSetting) {
            $sellerShopSetting->uid = str_unique();
        });
    }
}
