<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where("status", StatusEnum::true->status());
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, CountryZone::class, 'country_id', 'zone_id');
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
