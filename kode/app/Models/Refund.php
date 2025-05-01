<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    protected static function booted()
    {
        static::creating(function ($refund) {
            $refund->uid = str_unique();
        });
    }

}
