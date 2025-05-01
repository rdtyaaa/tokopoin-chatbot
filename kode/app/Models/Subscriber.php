<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Subscriber extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($subscriber) {
            $subscriber->uid = str_unique();
        });

        static::saved(function (Model $model) {
            Cache::forget(CacheKey::SUBSCRIBER->value);
        });
    }
}
