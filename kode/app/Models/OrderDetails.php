<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'attribute'  => 'object',
        'tax_amount' => 'object',
    ];

    protected static function booted()
    {
        static::creating(function ($orderDetails) {
            $orderDetails->uid = str_unique();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function digitalProductAttributeValue()
    {
        return $this->belongsTo(DigitalProductAttribute::class, 'digital_product_attribute_id');
    }

    public function scopeSellerOrderProduct($query)
    {
        return $query->whereHas('product', function($q){
            $q->whereNotNull('seller_id');
        });
    }

    public function scopeInhouseOrderProduct($query)
    {
        return $query->whereHas('product', function($q)
        {
            $q->where(function($q){
                return$q->whereNotNull('seller_id');
            })->orWhereNull('seller_id');
        }
    );
    }
}
