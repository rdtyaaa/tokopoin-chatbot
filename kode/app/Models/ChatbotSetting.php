<?php

namespace App\Models;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatbotSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'status',
        'mode',
        'delay_minutes',
        'response_delay',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}
