<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;
    const PLUS = '+';
    const MINUS = "-"; 

    protected $casts = [

        'guest_user' => 'object'
    ];


    protected $fillable = [
        'seller_id', 'user_id','deliveryman_id', 'amount', 'post_balance', 'transaction_type', 'transaction_number', 'details','uid','guest_user'
    ];


    public function scopeGuest($query)
    {
        return $query->whereNull('user_id')->whereNull('seller_id')->whereNull('deliveryman_id');
    }

    public function scopeUsers($query)
    {
        return $query->whereNotNull('user_id');
    }


    public function scopeSellers($query)
    {
        return $query->whereNotNull('seller_id');
    }


    public function scopeDeliverymen($query)
    {
        return $query->whereNotNull('deliveryman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    public function deliveryman()
    {
        return $this->belongsTo(DeliveryMan::class, 'deliveryman_id');
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->uid = str_unique();
        });
    }

    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){

             $searchBy = '%'. request()->input('search').'%';
             return $q->where('transaction_number',request()->input('search'))
                      ->orWhereHas('seller',function($q) use($searchBy){
                        return $q->where('name','like',$searchBy)
                           ->orWhere('email','like',$searchBy)
                           ->orWhere('username','like',$searchBy);
                      })
                      ->orWhereHas('user',function($q) use($searchBy){
                        return $q->where('name','like',$searchBy)
                           ->Orwhere('email','like',$searchBy)
                           ->Orwhere('username','like',$searchBy);
                      })->orWhere("guest_user->email",request()->input('search'))
                      ->orWhere("guest_user->first_name",request()->input('search'))
                      ->orWhere("guest_user->phone",request()->input('search'))
                      ->orWhere("guest_user->address",request()->input('search'))
                      ->orWhere("guest_user->city",request()->input('search'))
                      ->orWhere("guest_user->last_name",request()->input('search'))
                      ->orWhereHas('deliveryman',function($q) use($searchBy){
                        return $q->where('first_name','like',$searchBy)
                           ->Orwhere('email','like',$searchBy)
                           ->Orwhere('username','like',$searchBy);
                      });

            })->when(request()->input('delivery_man') ,function($q){
                return $q->whereNotNull('deliveryman_id')
                        ->where('deliveryman_id',request()->input('delivery_man'));
                            
            });
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
