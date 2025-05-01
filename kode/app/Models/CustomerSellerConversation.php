<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSellerConversation extends Model
{
    use HasFactory;


    protected $guarded = [];


    public function seller(){
        return $this->belongsTo(Seller::class,'seller_id','id');
    }

    public function customer(){
        return $this->belongsTo(User::class,'customer_id','id');
    }

    protected $casts = [
        'files' => 'object',
    ];
}
