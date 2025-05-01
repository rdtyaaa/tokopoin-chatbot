<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;

class Testimonial extends Model
{
    use HasFactory;


    protected static function booted()
    {
        static::creating(function ($support) {
            $support->uid = str_unique();
        });

        static::updated(function (Model $model) {
            
            Cache::forget(CacheKey::TESTIMONIAL->value);

        });
        static::saved(function (Model $model) {
            
            Cache::forget(CacheKey::TESTIMONIAL->value);

        });
        static::deleted(function (Model $model) {
            
            Cache::forget(CacheKey::TESTIMONIAL->value);

        });
    }
}
