<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];


    //get created  by info
    public function createdBy(){
        return $this->belongsTo(Admin::class,'created_by','id');
    }


    // get updated by info
    public function updatedBy(){
        return $this->belongsTo(Admin::class,'updated_by','id');
    }

    // get updated by info
    public function scopeActive($q){
        return $q->where('status',(StatusEnum::true)->status());
    }

    protected static function booted()
    {
        static::creating(function ($role) {
            $role->uid = str_unique();
        });
    }

}
