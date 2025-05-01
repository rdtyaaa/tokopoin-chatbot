<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function countries()
    {
        return $this->belongsToMany(Country::class, CountryZone::class, 'zone_id', 'country_id');
    }

    
    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::true->status());
    }
}
