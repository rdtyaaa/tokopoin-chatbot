<?php

namespace App\Models;

use App\Enums\KYCStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
class KycLog extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected $casts = [
        'custom_data' => 'object',
    ];


    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'deliveryman_id');
    }


    /**
     * Get pending log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopePending(Builder $q): Builder{
        return $q->where('status',KYCStatus::REQUESTED->value);
    }

    /**
     * Get approved log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeApproved(Builder $q): Builder{
        return $q->where('status',KYCStatus::APPROVED->value);
    }



    /**
     * Get hold log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeHold(Builder $q): Builder{
        return $q->where('status',KYCStatus::HOLD->value);
    }


    /**
     * Get rejected log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeRejected(Builder $q): Builder{
        return $q->where('status',KYCStatus::REJECTED->value);
    }



    
    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){

             $searchBy = '%'. request()->input('search').'%';
             return $q->whereHas('seller',function($q) use($searchBy){
                        return $q->where('name','like',$searchBy)
                           ->orWhere('email','like',$searchBy)
                           ->orWhere('username','like',$searchBy);
                      })->orWhereHas('deliveryMan',function($q) use($searchBy){
                        return $q->where('first_name','like',$searchBy)
                           ->orWhere('last_name','like',$searchBy)
                           ->orWhere('email','like',$searchBy)
                           ->orWhere('phone','like',$searchBy);
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

        if (!request()->date) return $query;

        $dateRangeString             = request()->date;
        $start_date                  = $dateRangeString;
        $end_date                    = $dateRangeString;
        if (strpos($dateRangeString, ' to ') !== false)  list($start_date, $end_date) = explode(" to ", $dateRangeString); 

        return $query->where(function ($query) use ($start_date, $end_date ,$column ) {
            $query->whereBetween($column , [$start_date, $end_date])
                ->orWhereDate($column , $start_date)
                ->orWhereDate($column , $end_date);
        });

    }


}
