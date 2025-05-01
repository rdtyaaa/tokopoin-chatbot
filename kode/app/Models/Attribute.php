<?php

namespace App\Models;

use App\Enums\BrandStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
use Illuminate\Database\Eloquent\Builder;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status','uid'];

    public function value()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }


    public function scopeActive(Builder $query)  :Builder {
        return $query->where('status', BrandStatus::ACTIVE);
   }

    protected static function booted()
    {
     
        static::creating(function (Model $product) {
            $product->uid = str_unique();
            Cache::forget(CacheKey::PRODUCT_ATTRIBUTE->value);
        });

        static::updated(function (Model $model) {
            Cache::forget(CacheKey::PRODUCT_ATTRIBUTE->value);
        });
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::PRODUCT_ATTRIBUTE->value);
        });
        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::PRODUCT_ATTRIBUTE->value);
    
        });
    }
}
