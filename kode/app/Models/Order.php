<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class Order extends Model
{
    use HasFactory;

    const PENDING = 0;
    const PLACED = 1;
    const CONFIRMED = 2;
    const PROCESSING = 3;
    const SHIPPED = 4;
    const DELIVERED = 5;
    const CANCEL = 6;
    const RETURN = 7;

    const UNPAID = 1;
    const PAID = 2;
    const WALLET_PAYMENT = 1;



    #PAYMENT TYPE
    const COD = 1;
    const PAYMENT_METHOD = 2;

    //Order Type
    const DIGITAL = 101;
    const PHYSICAL = 102;


    protected $guarded = [];

    protected $casts = [
        'billing_information'  => 'object',
        'payment_details' => 'object',
        'custom_information' => 'object',

    ];


    protected static function booted()
    {
        static::creating(function ($order) {
            $order->uid               = str_unique();
            $order->verification_code = generateOrderCode();
        });

        static::addGlobalScope('autoload', function (Builder $builder) {
            $builder->with(['billingAddress', 'billingAddress.country', 'billingAddress.state', 'billingAddress.city','deliveryManOrder','deliveryManOrder.deliveryMan']);
        });


        static::updated(function (Model $model) {

            Cache::forget(CacheKey::PHYSICAL_ORDER_COUNT->value);
            Cache::forget(CacheKey::PHYSICAL_SELLER_ORDER_COUNT->value);

        });
        static::saved(function (Model $model) {

            Cache::forget(CacheKey::PHYSICAL_ORDER_COUNT->value);
            Cache::forget(CacheKey::PHYSICAL_SELLER_ORDER_COUNT->value);

        });
        static::deleted(function (Model $model) {

            Cache::forget(CacheKey::PHYSICAL_ORDER_COUNT->value);
            Cache::forget(CacheKey::PHYSICAL_SELLER_ORDER_COUNT->value);

        });
    }



    public static function delevaryStatus() :array
    {
        return [
            'pending'     => Order::PENDING,
            'placed'      => Order::PLACED,
            'confirmed'   => Order::CONFIRMED,
            'processing'  => Order::PROCESSING,
            'shipped'     => Order::SHIPPED,
            'delivered'   => Order::DELIVERED,
            'cancel'      => Order::CANCEL,
            'return'      => Order::RETURN
        ];
    }


    public function scopePhysicalOrder($query)
    {
        return $query->where('order_type', self::PHYSICAL);
    }


    public function scopeDigitalOrder($query)
    {
        return $query->where('order_type', self::DIGITAL);
    }


    public function shipping()
    {
        return $this->belongsTo(ShippingDelivery::class, 'shipping_deliverie_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }



    public function deliveryman()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }



    public function billingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }



    public function orderRatings(){
        return $this->hasMany(DeliveryManRating::class, 'order_id');
    }


    
    public function deliveryManOrder(){
        return $this->hasOne(DeliveryManOrder::class, 'order_id');
    }



    public function scopePlaced($query)
    {
        return $query->where('status', self::PLACED);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::PROCESSING);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::SHIPPED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::DELIVERED);
    }

    public function scopeCancel($query)
    {
        return $query->where('status', self::CANCEL);
    }

    public function scopeReturn($query)
    {
        return $query->where('status', self::RETURN);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::PENDING);
    }


    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    public function scopeInhouseOrder($query)
    {
        return $query->whereHas('orderDetails', function($q){
            $q->whereHas('product', function($q){
                $q->whereNull('seller_id')->orWhereNotNull('seller_id');
            });
        });
    }


    public function scopeSellerOrder($query ,int | null $id = null) :Builder {
        return $query->whereHas('orderDetails', fn(Builder $q) =>
                                               $q->whereHas('product', fn(Builder $q)  => $q->whereNotNull('seller_id')
                                                             ->when($id ,fn (Builder $q) => $q->where('seller_id',$id) )));
    }



    public function scopeSpecificSellerOrder($query,$sellerId)
    {
        return $query->whereHas('orderDetails', function($q) use($sellerId){
            $q->whereHas('product', function($q)use($sellerId){
                $q->where('seller_id',$sellerId);
            });
        });
    }


    public function digitalProductOrder()
    {
        return $this->hasOne(OrderDetails::class, 'order_id');
    }


    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function refund()
    {
        return $this->hasMany(Refund::class, 'order_id','id')->latest();
    }
    public function log()
    {
        return $this->hasOne(PaymentLog::class, 'order_id','id');
    }


    public function orderStatus()
    {
        return $this->hasMany(OrderStatus::class, 'order_id','id')->latest();
    }




    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){
            $searchBy = '%'. request()->input('search').'%';
            return $q->where('order_id',request()->input('search'))->orWhereHas('customer',function($q) use($searchBy){
                        return $q->where('name',request()->input('search'))
                           ->orWhere('email',request()->input('search'))
                           ->orWhere('username',request()->input('search'));
                      })
                      ->orWhere("billing_information->email",request()->input('search'))
                      ->orWhere("billing_information->first_name",request()->input('search'))
                      ->orWhere("billing_information->phone",request()->input('search'))
                      ->orWhere("billing_information->address",request()->input('search'))
                      ->orWhere("billing_information->city",request()->input('search'))
                      ->orWhere("billing_information->last_name",request()->input('search'));

            })->when(request()->input('deliveryman_id'),function($q){
                return $q->where('delivery_man_id',request()->input('deliveryman_id'));
            });
    }
   /**
     * Date Filter
     *
     * @param Builder $query
     * @param string $column
     * @return Builder
     */
    public function scopeDate(Builder $query, string $column = 'created_at') : Builder {

        if (!request()->date) {
            return $query;
        }
        $dateRangeString             = request()->date;
        $start_date                  = $dateRangeString;
        $end_date                    = $dateRangeString;
        if (strpos($dateRangeString, ' to ') !== false) {
            list($start_date, $end_date) = explode(" to ", $dateRangeString);
        }

        return $query->where(function ($query) use ($start_date, $end_date ,$column ) {
                        $query->whereBetween($column , [$start_date, $end_date])
                            ->orWhereDate($column , $start_date)
                            ->orWhereDate($column , $end_date);
        });

    }
}
