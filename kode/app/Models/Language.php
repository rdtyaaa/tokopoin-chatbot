<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Enums\StatusEnum;

use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;class Language extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($language) {
            $language->uid = str_unique();
        });

        static::updated(function (Model $model) {
            Cache::forget(CacheKey::LANGUAGE->value);
        });
        static::saved(function (Model $model) {
            Cache::forget(CacheKey::LANGUAGE->value);
        });

        static::deleted(function (Model $model) {
            Cache::forget(CacheKey::LANGUAGE->value);
        });
    }


     //get created  by info
    public function createdBy(){
        return $this->belongsTo(Admin::class,'updated_by','id');
    }


    // get updated by info
    public function updatedBy(){
        return $this->belongsTo(Admin::class,'updated_by','id');
    }

    //default language
    public function scopeDefault($q){
        return $q->where('is_default',(StatusEnum::true)->status());
    }
    //active language
    public function scopeActive($q){
        return $q->where('status',(StatusEnum::true)->status());
    }
}
