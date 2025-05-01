<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureProduct extends Model
{
    use HasFactory;
    protected $guarded = [];    

    protected static function booted()
    {
        static::creating(function ($featureProduct) {
            $featureProduct->uid = str_unique();
        });
    }
}
