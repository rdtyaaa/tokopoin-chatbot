<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Frontend extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($frontend) {
            $frontend->uid = str_unique();
        });

        static::updated(function (Model $model) {

            Cache::forget(CacheKey::FRONTEND->value);
        });

        static::saved(function (Model $model) {
            
            Cache::forget(CacheKey::FRONTEND->value);
        });
    }


    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }



}
