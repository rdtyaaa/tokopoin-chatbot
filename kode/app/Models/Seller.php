<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Seller extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'status',
        'password',
        'balance',
        'image',
        'address',
        'uid'
    ];

    protected static function booted()
    {
        static::creating(function ($seller) {
            $seller->uid = str_unique();
        });

        static::updated(function (Model $model) {
            Cache::forget(CacheKey::FRONTEND_BEST_SELLER->value);
        });
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::FRONTEND_BEST_SELLER->value);
        });
        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::FRONTEND_BEST_SELLER->value);
        });
    }

    public function subscription()
    {
        return $this->hasMany(PlanSubscription::class, 'seller_id');
    }


    public function sellerShop()
    {
        return $this->hasOne(SellerShopSetting::class, 'seller_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'seller_id')->withTrashed();
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    public function withdraw()
    {
        return $this->hasMany(Withdraw::class, 'seller_id');
    }


    public function ticket()
    {
        return $this->hasMany(SupportTicket::class, 'seller_id');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    public function scopeBanned($query)
    {
        return $query->where('status', 2);
    }

    public function scopeProductWithTrashed($query, $id){
        return $query->where('seller_id', $id)->withTrashed();
    }

    public function follow()
    {
        return $this->hasMany(Follower::class, 'seller_id');
    }

    protected $casts = [
        'address' => 'object',
    ];


    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){
            $searchBy = '%'. request()->input('search').'%';
            return $q->where('name','like',$searchBy)
                        ->orWhere('phone',request()->input('search'))
                        ->orWhere('username',request()->input('search'))
                        ->orWhere('email',request()->input('search'));

            });
    }


    public function latestConversation(){
        return $this->hasOne(CustomerSellerConversation::class,'seller_id','id')
                                   ->latest();
    }




}
