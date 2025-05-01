<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'address' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class,'address_id');
    }


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
