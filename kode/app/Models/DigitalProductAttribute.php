<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
class DigitalProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'short_details',
        'uid'
    ];




    /**
     * Get attribute value
     *
     * @return HasMany
     */
    public function digitalProductAttributeValueKey() :HasMany {
        return $this->hasMany(DigitalProductAttributeValue::class, 'digital_product_attribute_id')->latest();
    }

    /**
     * Get product
     *
     * @return hasMany
     */
    public function attribute() : hasMany {
        return $this->hasMany(Product::class, 'product_id');
    }


    /**
     * Get available attribute
     *
     * @param Builder $query
     * @return Builder
     * 
     */
    public function scopeAvailable(Builder $query) :Builder {
        return $query->where('status', StatusEnum::true->status());
    }

    protected static function booted(){
        static::creating(function (Model $digitalProductAttribute) {
            $digitalProductAttribute->uid = str_unique();
        });
    }
}
