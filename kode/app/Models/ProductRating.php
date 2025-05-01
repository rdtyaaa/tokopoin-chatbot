<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    use HasFactory;
    protected $guarded = [];    

    protected static function booted()
    {
        static::creating(function ($productRating) {
            $productRating->uid = str_unique();
        });
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
