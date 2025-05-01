<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $casts = [
        'agent_info' => 'object',
    ];


    protected $guarded = [];




    public static function insertOrupdtae(string  $ip_address,array $ipInfo , bool $blocked = false) :mixed{

        $ip = Visitor::where('ip_address', $ip_address)->first();
        if (!$ip) {
            $ip = new Visitor();
            $ip->ip_address = $ip_address;
            $ip->is_blocked = $blocked ? StatusEnum::true->status() : StatusEnum::false->status();
        }

        $ip->agent_info = $ipInfo;
        $ip->updated_at =  Carbon::now();
        $ip->save();
        return $ip;
    }
    
}