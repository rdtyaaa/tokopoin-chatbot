<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'uid'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected static function booted()
    {
        static::creating(function ($wishList) {
            $wishList->uid = str_unique();
        });
    }
}
