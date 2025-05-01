<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryZone extends Model
{
    use HasFactory;

    protected $table   = 'country_zone';
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
