<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;


    
    const ACTIVE = 1;
    const INACTIVE = 2;


    
    public function discount($total)
    { 

        $amount = 0;
    
        if($this->type == 1){
            $amount =  ($this->value);
        }elseif($this->type == 2){
            $amount =  (($this->value) / 100 ) * $total;
        }

        if($amount > $total){
            return $total - 1;
        }
        return $amount;
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($coupon) {
            $coupon->uid = str_unique();
        });
    }



    /**
     * Get active coupon
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) :Builder{
        return $query->where('status',SELF::ACTIVE);
    }


    /**
     * Get valid coupon
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeValid(Builder $query) :Builder {
        
        $now = Carbon::now();
        return $query->active()
                            ->where(fn(Builder $query) =>  $query->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now)
                            ->where('status', 1) );
    }

    
}
