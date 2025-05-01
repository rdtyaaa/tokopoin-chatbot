<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class SupportTicket extends Model
{
    use HasFactory;

    const LOW = 1;
    const MEDIUM = 2;
    const HIGH = 3;


    const CLOSED = 4;

    protected $guarded = [];


    public static function priority() :array 
    {
        return [
            'Low'         => SELF::LOW,
            'Medium'      => SELF::MEDIUM,
            'High'        => SELF::HIGH,

        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }


    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'deliveryman_id');
    }


    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){
            $searchBy = '%'. request()->input('search').'%';
            return $q->where('name','like',$searchBy)
                    ->orWhere('email','like',$searchBy)
                    ->orWhere('subject','like',$searchBy)
                    ->orWhere('ticket_number',request()->input('search'));
                        
            })->when(request()->input('type') &&  request()->input('type') != 0,function($q){
                $type = request()->input('type');
                return $q->when( $type == 1,function($q){
                    return $q->whereNotNull('user_id');
                })->when($type == 2,function($q){
                    return $q->whereNotNull('seller_id');
                })
                ->when($type == 3,function($q){
                    return $q->whereNotNull('deliveryman_id');
                });;
                            
            });
    }


    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'support_ticket_id', 'id')->latest();
    }

    protected static function booted()
    {
        static::creating(function ($supportTicket) {
            $supportTicket->uid = str_unique();
        });
        static::updated(function (Model $model) {
            Cache::forget(CacheKey::RUNNING_TICKET->value);
        });
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::RUNNING_TICKET->value);
        });
        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::RUNNING_TICKET->value);
        
        });
    }
}
