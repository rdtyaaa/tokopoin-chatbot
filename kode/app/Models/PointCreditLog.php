<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointCreditLog extends Model
{
    use HasFactory;


    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
