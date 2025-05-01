<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShippingDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shipping_delivery_id',
        'uid'
    ];

    protected static function booted()
    {
        static::creating(function ($productShippingDelivery) {
            $productShippingDelivery->uid = str_unique();
        });
    }


    public function shippingDelivery()
    {
    	return $this->belongsTo(ShippingDelivery::class, 'shipping_delivery_id');
    }
}
