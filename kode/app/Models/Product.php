<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Product extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $casts = [
        'shipping_country' => 'object',
        'meta_keywords'    => 'object',
        'rating'           => 'json',
        'custom_fileds'    => 'object'
    ];

    protected $dates = ['deleted_at'];

    const NEW = 0;
    const PUBLISHED = 1;
    const INACTIVE = 2;
    const CANCEL = 3;

    #Product Type
    const DIGITAL = 101;
    const PHYSICAL = 102;

    #Top Product Status
    const NO = '1';
    const YES = '2';

    protected $fillable = [
        'product_type',
        'seller_id',
        'brand_id',
        'category_id',
        'sub_category_id',
        'name',
        'slug',
        'price',
        'discount',
        'discount_percentage',
        'featured_image',
        'short_description',
        'description',
        'warranty_policy',
        'minimum_purchase_qty',
        'maximum_purchase_qty',
        'featured_status',
        'top_status',
        'best_selling_item_status',
        'status',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'meta_image',
        'rating',
        'uid',
        'weight',
        'shipping_fee',
        'shipping_fee_multiply',
        'custom_fileds',
        'point',
    ];




    protected static function booted(){

        static::creating(function (Model $product) {
            $product->uid = str_unique();
            self::forgetCacheKey();
        });

        static::updated(function (Model $model) {
            self::forgetCacheKey();
        });
        static::saved(function (Model $model) {
            self::forgetCacheKey();
        });
        static::deleted(function (Model $model) {
            self::forgetCacheKey();

        });

        static::addGlobalScope('autoload', function (Builder $builder) {
            $builder->with(['taxes']);
        });
    }


    public static function forgetCacheKey(){
        Cache::forget(CacheKey::FRONTEND_CATEGORIES->value);
        Cache::forget(CacheKey::TOP_CATEGORIES->value);
        Cache::forget(CacheKey::SELLER_NEW_DIGITAL_PRODUCT->value);
        Cache::forget(CacheKey::SELLER_NEW_PHYSICAL_PRODUCT->value);
        Cache::forget(CacheKey::FRONTEND_NEW_PRODUCTS->value);
        Cache::forget(CacheKey::FRONTEND_TODAYS_DEAL_PRODUCTS->value);
        Cache::forget(CacheKey::FRONTEND_DIGITAL_PRODUCTS->value);
        Cache::forget(CacheKey::TOP_BRANDS->value);
        Cache::forget(CacheKey::FRONTEND_BEST_SELLING_PRODUCTS->value);
        Cache::forget(CacheKey::FRONTEND_TOP_PRODUCTS->value);
        Cache::forget(CacheKey::FRONTEND_BEST_SELLER->value);
        Cache::forget(CacheKey::ALL_CATEGORIES->value);
        Cache::forget(CacheKey::ALL_BRANDS->value);
    }

    /**
     * Get feature product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFeatured(Builder $query) :Builder {
        return $query->where('featured_status', self::YES);
    }


    /**
     * Get top product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTop(Builder $query) :Builder {
        return $query->where('top_status', self::YES);
    }


    /**
     * Get new product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNew(Builder $query) :Builder {
        return $query->where('status', self::NEW);
    }


    /**
     * Get published product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished(Builder $query) :Builder {
        return $query->where('status', self::PUBLISHED);
    }


    /**
     * Get all inactive product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive(Builder $query) :Builder {
        return $query->where('status', self::INACTIVE);
    }

    /**
     * Get all inhouse product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInhouseProduct(Builder $query) :Builder {
        return $query->whereNull('seller_id');
    }



    /**
     * Get all seller product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSellerProduct(Builder $query) :Builder {
        return $query->whereNotNull('seller_id');
    }


    /**
     * Get all digital product
     *
     * @param Builder $he
     * @return Builder
     */
    public function scopeDigital(Builder $query) :Builder {
        return $query->where('product_type', self::DIGITAL);
    }



    /**
     * Summary of getWeightAttribute
     * @param mixed $value
     * @return string
     */
    public function getWeightAttribute($value)
    {
        $deciamlDigit = site_settings('digit_after_decimal', 2);
        return number_format($value, $deciamlDigit);
    }

    /**
     * Get all physical product
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePhysical(Builder $query) :Builder {
        return $query->where('product_type', self::PHYSICAL);
    }


    /**
     * Get the category of the product
     *
     * @return BelongsTo
     */
    public function category() :BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }


    /**
     * Get the subcategory name of the product
     *
     * @return BelongsTo
     */
    public function subCategory() :BelongsTo {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }


    /**
     * Get the brand name of the product
     *
     * @return BelongsTo
     */
    public function brand() :BelongsTo {
        return $this->belongsTo(Brand::class, 'brand_id');
    }


    /**
     * Get product gallery images
     *
     * @return HasMany
     */
    public function gallery() :HasMany {
        return $this->hasMany(ProductImage::class, 'product_id','id');
    }


    /**
     * Get product stock
     *
     * @return HasMany
     */
    public function stock() :HasMany {
        return $this->hasMany(ProductStock::class, 'product_id');
    }


    /**
     * Get the seller name of the product
     *
     * @return BelongsTo
     */
    public function seller() :BelongsTo {
        return $this->belongsTo(Seller::class, 'seller_id');
    }




    /**
     * Get all product order
     *
     * @return HasMany
     */
    public function order() :HasMany {
        return $this->hasMany(OrderDetails::class, 'product_id');
    }


    /**
     * Get digital product attributes
     *
     * @return HasMany
     */
    public function digitalProductAttribute() :HasMany {
        return $this->hasMany(DigitalProductAttribute::class, 'product_id')->latest();
    }


    /**
     * Get all product ratings
     *
     * @return HasMany
     */
    public function rating() :HasMany {
        return $this->hasMany(ProductRating::class, 'product_id','id');
    }

    /**
     * Get all product reviews
     *
     * @return HasMany
     */
    public function review() :HasMany {
        return $this->hasMany(ProductRating::class, 'product_id','id');
    }


    /**
     * Get product wishlist items
     *
     * @return HasMany
     */
    public function wishlist() :HasMany {
        return $this->hasMany(WishList::class, 'product_id');
    }


    /**
     * Get product shipping delivaries
     *
     * @return HasMany
     */
    public function shippingDelivery() :HasMany {
        return $this->hasMany(ProductShippingDelivery::class, 'product_id');
    }

    /**
     * Get product exclusive offers
     *
     * @return HasMany
     */
    public function exoffer() :HasMany
    {
        return $this->hasMany(ExclusiveOffer::class, 'product_id','id');
    }


    /**
     * Product campaingns
     *
     * @return BelongsToMany
     */
    public function campaigns() :BelongsToMany {
      return $this->belongsToMany(Campaign::class,CampaignProduct::class,'product_id','campaign_id')
                           ->withPivot(['discount_type','discount']);
    }




    /**
     * Search product by request input
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeSearch(Builder $q) :Builder {

        return $q->when(request()->input('search'),function($q){
            $searchBy = '%'. request()->input('search').'%';
                return $q->where('name','like',$searchBy)
                        ->orWhereHas('category',function($q) use($searchBy){
                            $locale = session()->get('locale','en');
                            return $q->where('name->'.$locale,'like',$searchBy);
                        })->orWhereHas('brand',function($q) use($searchBy){
                            $locale = session()->get('locale','en');
                            return $q->where('name->'.$locale,'like',$searchBy);
                        });

            })->when(request()->input('search_max'),function($q){
                return $q->whereBetween('price', [convert_to_base(request()->input('search_min')),convert_to_base(request()->input('search_max'))]);
            })->when(request()->input('sort_by') ,function($query) {

                if(request()->input('sort_by') == "hightolow"){
                    $query->orderByRaw("CASE WHEN discount!=0 THEN discount ELSE price END DESC");
                }
                elseif(request()->input('sort_by') == "lowtohigh"){
                    $query->orderByRaw("CASE WHEN discount!=0 THEN discount ELSE price END ASC");
                }
                else{
                    $query->latest();
                }
            })
            ->when(request()->input('seller_id') ,function($query) {
                $query->where('seller_id',request()->input('seller_id'));
            })
            ->when(!request()->input('sort_by') ,function($query) {
                    $query->latest();
            });
    }




    /**
     * Product taxes
     *
     * @return BelongsToMany
     */
    public function taxes() :BelongsToMany {
        return $this->belongsToMany(Tax::class,ProductTax::class,'product_id','tax_id')
                             ->withPivot(['amount','type']);
     }





}
