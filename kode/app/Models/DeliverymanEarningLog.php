<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class DeliverymanEarningLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order(){
        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function deliveryMan(){
        return $this->belongsTo(DeliveryMan::class,'deliveryman_id','id');
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



    public function scopeFilter($q)
    {
        return $q->when(request()->input('order_number'),function($q){
            return $q->whereHas('order',function($q) {
                        return $q->where('order_id',request()->input('order_number'));
                      });

            });
    }
}
