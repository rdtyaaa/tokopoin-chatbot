<?php

namespace App\Models;

use App\Enums\BrandStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
   use HasFactory;


   protected $guarded = [];


   use HasFactory;

   const NO = 1;
   const YES = 2;


   protected $fillable = [
      'serial',
      'name',
      'logo',
      'status',
      'slug',
      'uid'
   ];


   public function scopeActive(Builder $query)  :Builder {
        return $query->where('status', BrandStatus::ACTIVE);
   }


   public function product()
   {
      return $this->hasMany(Product::class, 'brand_id');
   }

   public function houseProduct()
   {
       return $this->hasMany(Product::class, 'brand_id')
                        ->where('product_type', '102')
                        ->where(function ($query) {
                            $query->whereNull('seller_id')
                                ->whereIn('status', [0, 1])
                                ->orWhereNotNull('seller_id')
                                ->whereIn('status', [1]);
                        });
   }
   protected static function booted()
   {
       static::creating(function ($brand) {
           $brand->uid = str_unique();
       });


        static::updated(function (Model $model) {
                
            Cache::forget(CacheKey::TOP_BRANDS->value);
            Cache::forget(CacheKey::ALL_BRANDS->value);

   
        });
        static::saved(function (Model $model) {
            
            Cache::forget(CacheKey::TOP_BRANDS->value);
            Cache::forget(CacheKey::ALL_BRANDS->value);


        });
        static::deleted(function (Model $model) {
            
            Cache::forget(CacheKey::TOP_BRANDS->value);
            Cache::forget(CacheKey::ALL_BRANDS->value);
   
        });
   }
}
