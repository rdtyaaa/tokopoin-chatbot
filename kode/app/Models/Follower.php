<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;
    protected $guarded = [];    


    public function seller()
    {
        return $this->belongsTo(Seller::class, 'following_id');
    }

    protected static function booted()
    {
        static::creating(function ($follower) {
            $follower->uid = str_unique();
        });
    }
}
