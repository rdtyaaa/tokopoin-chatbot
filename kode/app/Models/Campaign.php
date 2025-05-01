<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * campaign product 
     * 
     */

     public function products(){
        return $this->belongsToMany(Product::class,CampaignProduct::class,'campaign_id','product_id')->withPivot(['discount_type','discount']);
     }

     protected static function booted()
     {
         static::creating(function ($campaign) {
             $campaign->uid = str_unique();
         });
     }


     public function scopeSearch($q)
     {
         return $q->when(request()->input('search'),function($q){
 
              $searchBy = '%'. request()->input('search').'%';
              return $q->where('name','like',$searchBy);;
 
             });
     }
 

}
