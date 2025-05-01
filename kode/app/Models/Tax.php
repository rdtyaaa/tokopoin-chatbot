<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Tax extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where("status", StatusEnum::true->status());
    }




     public function products(){
        return $this->belongsToMany(Product::class,ProductTax::class,'tax_id','product_id')
                   ->withPivot(['amount','type']);
     }
}
