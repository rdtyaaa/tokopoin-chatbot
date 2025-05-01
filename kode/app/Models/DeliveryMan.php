<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
class DeliveryMan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];


    protected $casts = [
        'kyc_data' => 'object',
        'address'  => 'object',
        'last_login_time' => 'datetime',
    ];


    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){
             $searchBy = '%'. request()->input('search').'%';
             return  $q->where('first_name','like',$searchBy)
                        ->orWhere('email',request()->input('search'))
                        ->orWhere('username',request()->input('search'))
                        ->orWhere('phone',request()->input('search'));
            });
    }


    // get updated by info
    public function scopeActive($q){
        return $q->where('status',(StatusEnum::true)->status());
    }


    public function country() {
        return $this->belongsTo(Country::class,'country_id','id');
    }


    public function orders() {
        return $this->hasMany(DeliveryManOrder::class,'deliveryman_id','id');
    }


    public function ratings() {
        return $this->hasMany(DeliveryManRating::class,'delivery_men_id','id');
    }




    public function latestConversation(){
        return $this->hasOne(CustomerDeliverymanConversation::class,'deliveryman_id','id')
                                   ->latest();
    }



    public function latestSenderMessage(){
        return $this->hasOne(DeliveryManConversation::class,'sender_id','id')->latest();
    }


    
    public function latestReceiverMessage(){
        return $this->hasOne(DeliveryManConversation::class,'receiver_id','id')->latest();
    }



    public function refferedBy(): BelongsTo {
        return $this->belongsTo(self::class,'referral_id','id');
    }



     /**
     * Date Filter
     *
     * @param Builder $query
     * @param string $column
     * @return Builder
     */
    public function scopeDate(Builder $query, string $column = 'created_at') : Builder {

        if (!request()->date) {
            return $query;
        }
        $dateRangeString             = request()->date;
        $start_date                  = $dateRangeString;
        $end_date                    = $dateRangeString;
        if (strpos($dateRangeString, ' to ') !== false) {
            list($start_date, $end_date) = explode(" to ", $dateRangeString);
        }

        return $query->where(function ($query) use ($start_date, $end_date ,$column ) {
                        $query->whereBetween($column , [$start_date, $end_date])
                            ->orWhereDate($column , $start_date)
                            ->orWhereDate($column , $end_date);
        });

    }

}
