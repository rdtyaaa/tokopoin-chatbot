<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'currency_id',
        'duration',
        'rate',
        'min_limit',
        'max_limit',
        'fixed_charge',
        'percent_charge',
        'description',
        'user_information',
        'status',
        'uid'
    ];

    protected $casts = [
        'user_information' => 'object'
    ];


    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function withdarwLogs()
    {
        return $this->hasMany(Withdraw::class, 'method_id','id');
    }


    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('status', StatusEnum::true->status());
    }

    protected static function booted()
    {
        static::creating(function ($withdrawMethod) {
            $withdrawMethod->uid = str_unique();
        });
    }
}
