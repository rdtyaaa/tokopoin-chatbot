<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompareProductList extends Model
{
    use HasFactory;
    protected $guarded = [];    

    public function product()
    {
    	return $this->belongsTo(Product::class, 'product_id');
    }
    protected static function booted()
    {
        static::creating(function ($compareProductList) {
            $compareProductList->uid = str_unique();
        });
    }
}
