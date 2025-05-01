<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplates extends Model
{
    use HasFactory;
    protected $guarded = [];    


    protected $casts = [
        'codes' => 'object'
    ];

    protected static function booted()
    {
        static::creating(function ($emailTemplates) {
            $emailTemplates->uid = str_unique();
        });
    }
}
