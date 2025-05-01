<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashDeal extends Model
{
    use HasFactory;
    protected $guarded = [];   
    
    protected $casts = [
        'products' => 'array'
    ];
    
    protected static function booted(){
        static::creating(function ($model) {
            $model->uid = str_unique();
        });
    }


}
