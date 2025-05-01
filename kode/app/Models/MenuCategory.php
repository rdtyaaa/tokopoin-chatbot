<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;

class MenuCategory extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    protected static function booted()
    {
       

        static::saved(function (Model $model) {
            Cache::forget(CacheKey::MENU_CATEGORY->value);
        });


        static::updated(function (Model $model) {
            Cache::forget(CacheKey::MENU_CATEGORY->value);
        });

        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::MENU_CATEGORY->value);
        });
    }
}
