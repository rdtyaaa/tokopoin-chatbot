<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalProductAttributeValue extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const SOLD = 2;


    protected $fillable = [
        'digital_product_attribute_id',
        'value',
        'name',
        'file',
        'status',
        'uid'
    ];

    protected static function booted()
    {
        static::creating(function ($digitalProductAttributeValue) {
            $digitalProductAttributeValue->uid = str_unique();
        });
    }

}
