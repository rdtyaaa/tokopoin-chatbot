<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class PageSetup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'uid'
    ];

    protected static function booted()
    {
        static::creating(function ($pageSetup) {
            $pageSetup->uid = str_unique();
        });
        
        static::updated(function (Model $model) {
            Cache::forget(CacheKey::PAGES->value);
        });
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::PAGES->value);
        });
        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::PAGES->value);
        });
    }


    /**
     * Get active coupon
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) :Builder{
        return $query->where('status',StatusEnum::true->status());
    }
}
