<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFile extends Model
{
    protected $guarded = [];
    use HasFactory;
    protected static function booted()
    {
        static::creating(function ($supportFile) {
            $supportFile->uid = str_unique();
        });
    }
}
