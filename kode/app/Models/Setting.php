<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected static function booted()
    {

        static::saved(function (Model $model) {
            Cache::forget(CacheKey::SITE_SETTINGS->value);
        });

    }


}
