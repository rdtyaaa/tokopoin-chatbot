<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table = "password_resets";

    protected $fillable = [
    	'email',
    	'token',
        'uid'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::creating(function ($passwordReset) {
            $passwordReset->uid = str_unique();
        });
    }

}
