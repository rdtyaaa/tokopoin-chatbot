<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'display_name',
        'name',
        'uid'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    protected static function booted()
    {
        static::creating(function ($attributeValue) {
            $attributeValue->uid = str_unique();
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
