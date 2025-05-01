<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Faq extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($faq) {
            $faq->uid = str_unique();
        });


        static::saved(function (Model $model) {
            Cache::forget(CacheKey::FAQ->value);
        });


        static::updated(function (Model $model) {
            Cache::forget(CacheKey::FAQ->value);
        });

        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::FAQ->value);
        });
    }
}
