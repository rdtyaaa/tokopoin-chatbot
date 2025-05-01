<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'slug',
        'url',
        'uid',
        'banner_image'
    ];

    public function menuCategory()
    {
        return $this->hasMany(MenuCategory::class, 'menu_id')->orderBy('serial', 'ASC');
    }   

    protected static function booted()
    {
        static::creating(function ($menu) {
            $menu->uid = str_unique();
        });

        
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::MENU->value);
        });

        static::updated(function (Model $model) {
            Cache::forget(CacheKey::MENU->value);
        });
        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::MENU->value);
        });
    }
}
