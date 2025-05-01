<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * cart camapgin
     */
    public function campaigns()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }


    protected $fillable =  [
        'user_id',
        'campaign_id',
        'session_id',
        'product_id',
        'quantity',
        'price',
        'attribute',
        'uid',
        'total',
        'attributes_value',
        'taxes',
        'original_price',
        'discount',
        'total_taxes'
    ];


    protected $casts = [
        'attribute' => 'object',
        'taxes' => 'object',
    ];

    protected static function booted()
    {
        static::creating(function ($cart) {
            $cart->uid = str_unique();
        });
    }

    /** cart filter */

    public function scopeFilter($q,$user,$sessionId){

        return $q->when($user && $sessionId  ,  function ($q) use ($user,$sessionId) {
            return $q->where('user_id', $user->id);
        })->when($sessionId ,  function ($q) use ($sessionId) {
            return $q->where('session_id', $sessionId);
        });
    }
}
