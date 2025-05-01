<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginInfo extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected static function booted()
    {
        static::creating(function ($userLoginInfo) {
            $userLoginInfo->uid = str_unique();
        });
    }

}
