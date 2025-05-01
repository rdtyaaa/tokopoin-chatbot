<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;


    protected $fillable = [
        'serial',
        'name',
        'parent_id',
        'banner',
        'image_icon',
        'meta_title',
        'meta_description',
        'meta_image',
        'status',
        'top',
        'slug',
        'uid'
    ];


    public function scopeActive(Builder $query)  :Builder {
        return $query->where('status', StatusEnum::true->status());
    }




    

    public function scopeParentCategory($query)
    {
        return $query->whereNull('parent_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }




    public function children() : HasMany {

        return $this->hasMany(Category::class, 'parent_id')->latest();
    }







    public function scopeTop($q)
    {
        return $q->where('top','1');
    }

    public function parent()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function digitalProduct()
    {
        return $this->hasMany(Product::class, 'category_id')->digital();

    }
    public function physicalProduct()
    {
        return $this->hasMany(Product::class, 'category_id')->physical();

    }
    public function houseProduct()
    {
        return $this->hasMany(Product::class, 'category_id')->where('product_type','102')
        ->where(function ($query) {
            $query->whereNull('seller_id')
                ->whereIn('status', [0, 1])
                ->orWhereNotNull('seller_id')
                ->whereIn('status', [1]);
        });
    }
    public function houseSubCateProduct()
    {
        return $this->hasMany(Product::class, 'sub_category_id')->where('product_type','102')
                                ->where(function ($query) {
                                    $query->whereNull('seller_id')
                                        ->whereIn('status', [0, 1])
                                        ->orWhereNotNull('seller_id')
                                        ->whereIn('status', [1]);
                                });
    }
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->uid = str_unique();
        });

        
        static::updated(function (Model $model) {
            
            Cache::forget(CacheKey::TOP_CATEGORIES->value);
            Cache::forget(CacheKey::FRONTEND_CATEGORIES->value);
            Cache::forget(CacheKey::ALL_CATEGORIES->value);
        });
        static::saved(function (Model $model) {
            
            Cache::forget(CacheKey::TOP_CATEGORIES->value);
            Cache::forget(CacheKey::FRONTEND_CATEGORIES->value);
            Cache::forget(CacheKey::ALL_CATEGORIES->value);
        });
        static::deleted(function (Model $model) {
            
            Cache::forget(CacheKey::TOP_CATEGORIES->value);
            Cache::forget(CacheKey::FRONTEND_CATEGORIES->value);
            Cache::forget(CacheKey::ALL_CATEGORIES->value);
        });
    }
}
