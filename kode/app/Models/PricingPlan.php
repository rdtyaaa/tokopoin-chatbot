<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'total_product',
        'duration', 
        'status',
        'uid'
    ];

    public function plansubcription()
    {
        return $this->hasMany(PlanSubscription::class, 'plan_id','id');
    }
    protected static function booted()
    {
        static::creating(function ($pricingPlan) {
            $pricingPlan->uid = str_unique();
        });
    }


    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){
            $searchBy = '%'. request()->input('search').'%';
            return $q->where('name','like',$searchBy);

            });
    }


    

    
    /**
     * Get all digital product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) :Builder {
        return $query->where('status',StatusEnum::true->status());
    }


}
