<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;

class Currency extends Model
{
    use HasFactory;

    protected $fillable  = [
        'name',
        'symbol',
        'status',
        'rate',
        'uid'
    ];




    public  function withdraw(){
       return $this->hasMany(WithdrawMethod::class,'currency_id','id');
    }   
    public  function paymentMethods(){
       return $this->hasMany(PaymentMethod::class,'currency_id','id');
    }   
    protected static function booted()
     {
         static::creating(function ($currency) {
             $currency->uid = str_unique();
         });

        static::updated(function (Model $model) {
            
            Cache::forget(CacheKey::CURRENCIES->value);
        });
         static::saved(function (Model $model) {
            
            Cache::forget(CacheKey::CURRENCIES->value);
        });
         static::deleted(function (Model $model) {
            
            Cache::forget(CacheKey::CURRENCIES->value);
        });

     }

     public function scopeActive($query)
     {
         return $query->where('status', '1');
     }

     public function scopeDefault($query)
     {
         return $query->where('default', '1');
     }

}
