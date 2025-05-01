<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailConfiguration extends Model
{
    use HasFactory;  

    protected static function booted()
    {
        static::creating(function ($mailConfiguration) {
            $mailConfiguration->uid = str_unique();
        });
    }

    protected $table = "mails";

    protected $casts = [
        'driver_information' => 'object',
    ];
}
