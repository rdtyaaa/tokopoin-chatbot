<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Banner extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($banner) {
            $banner->uid = str_unique();
        });


        static::updated(function (Model $model) {
            Cache::forget(CacheKey::BANNERS->value);
        });
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::BANNERS->value);
        });
        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::BANNERS->value);
        });
    }

    /**
     * active banner
     */

     public function scopeActive($q){
        return $q->where('status','1');
     }
}
