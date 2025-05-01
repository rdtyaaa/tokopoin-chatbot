<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class PlanSubscription extends Model
{
    use HasFactory;

    const RUNNING = 1;
    const EXPIRED = 2;
    const REQUESTED = 3;

    protected $fillable = [
        'seller_id', 'plan_id', 'status', 'expired_date','total_product'
    ];

    public function plan()
    {
        return $this->belongsTo(PricingPlan::class, 'plan_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    protected $dates = ['expired_date'];
    
    protected static function booted()
    {
        static::creating(function ($planSubscription) {
            $planSubscription->uid = str_unique();
        });
    }



    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){

             $searchBy = '%'. request()->input('search').'%';
             return $q->WhereHas('seller',function($q) use($searchBy){
                        return $q->where('name','like',$searchBy)
                           ->Orwhere('email','like',$searchBy)
                           ->Orwhere('username','like',$searchBy);
                      });

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
