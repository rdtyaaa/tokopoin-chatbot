<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }


    public function scopeVisible(Builder $q): Builder
    {
        return $q->where("status", StatusEnum::true->status());
    }
}
