<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExclusiveOffer extends Model
{
    use HasFactory;
    protected $guarded = [];    


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    protected static function booted()
    {
        static::creating(function ($exclusiveOffer) {
            $exclusiveOffer->uid = str_unique();
        });
    }
}
