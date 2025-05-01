<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDelivery extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_configuration' => 'object',
    ];



    public function scopeActive(Builder $query)  :Builder {
        return $query->where('status', StatusEnum::true->status());
    }




    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'method_id');
    }
    public function order()
    {
        return $this->hasMany(Order::class, 'shipping_deliverie_id' ,'id');
    }
    protected static function booted()
    {
        static::creating(function ($shippingDelivery) {
            $shippingDelivery->uid = str_unique();
        });
    }
}
