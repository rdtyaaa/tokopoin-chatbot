<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function supportfiles()
    {
        return $this->hasMany(SupportFile::class, 'support_message_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($supportMessage) {
            $supportMessage->uid = str_unique();
        });
    }
}
