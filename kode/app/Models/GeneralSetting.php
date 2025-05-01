<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'codes' => 'otp_configuration'
    ];

    protected static function booted()
    {
        static::creating(function ($generalSetting) {
            $generalSetting->uid = str_unique();
        });
    }

}
