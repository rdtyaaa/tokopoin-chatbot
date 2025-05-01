<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDeliverymanConversation extends Model
{
    use HasFactory;


    protected $guarded = [];


    public function deliveryMan(){
        return $this->belongsTo(DeliveryMan::class,'deliveryman_id','id');
    }



    public function customer(){
        return $this->belongsTo(User::class,'customer_id','id');
    }



    protected $casts = [
        'files' => 'object',
    ];





}
