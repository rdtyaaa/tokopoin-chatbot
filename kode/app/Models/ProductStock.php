<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'attribute_id', 
        'attribute_value', 
        'stock',
        'uid'
    ];

    protected static function booted()
    {
        static::creating(function ($productStock) {
            $productStock->uid = str_unique();
        });
    }


    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
