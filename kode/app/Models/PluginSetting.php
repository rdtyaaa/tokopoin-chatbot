<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PluginSetting extends Model
{
    use HasFactory;
    protected $guarded = [];    

    protected static function booted()
    {
        static::creating(function ($pluginSetting) {
            $pluginSetting->uid = str_unique();
        });
    }
}
