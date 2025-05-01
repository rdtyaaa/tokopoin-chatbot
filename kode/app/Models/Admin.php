<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Role;

class Admin extends Authenticatable
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


    //get admin roles
    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }
    
    protected static function booted()
    {
        static::creating(function ($admin) {
            $admin->uid = str_unique();
        });
    }
}
