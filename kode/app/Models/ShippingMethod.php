<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'image',
        'status',
        'uid'
    ];

    public function shippingdelivery()
    {
        return $this->hasMany(ShippingDelivery::class, 'method_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($shippingMethod) {
            $shippingMethod->uid = str_unique();
        });
    }
}
