<?php

namespace App\Http\Resources\Seller;

use App\Http\Resources\TaxCollection;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        $reviews =  $this->review->map( fn(ProductRating $review) :array  => [
                'user'    => $review->customer?->name,
                'profile' => show_image(file_path()['profile']['user']['path'].'/'.$review->customer?->image),
                'review'  =>  $review->review,
                "rating" => (int) $review->rating 
        ]);


        $status = '';

        switch ($this->status) {
            case Product::PUBLISHED:
                $status = "Published";
                break;
            case Product::INACTIVE:
                $status = "Inactive";
                break;
            case Product::CANCEL:
                $status = "Cancel";
                break;
            case Product::NEW:
                $status = "New";
                break;
        }



        return [

            'id'              => $this->id,
            'uid'             => $this->uid,
            'seller_id'       => $this->seller_id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'product_type'    => $this->product_type == Product::PHYSICAL ? 'Physical' : 'Digital',
            'type_enum'       => $this->product_type ,
            'weight'           => round($this->weight,site_settings('digit_after_decimal',2)),
            'shipping_fee'           => round($this->shipping_fee,site_settings('digit_after_decimal',2)),
            'shipping_fee_multiply_by_qty'           => $this->shipping_fee == 0 ? false : true,
            'warranty_policy' => $this->warranty_policy,
            'brand'           => new BrandResource($this->brand),
            'category'        => new CategoryResource($this->category),
            'sub_category'    => new CategoryResource($this->subCategory),

    

            'total_order_count'         => $this->order->count(),
            'total_delivered_order'     => $this->order->where('status', Order::DELIVERED)->count(),
            'total_placed_order'        => $this->order->where('status', Order::PLACED)->count(),
            'total_order_amount'        => api_short_amount(@$this->order?->sum('total_price')  ?? 0),
            'price'                     => (double) $this->price,
            'discount'                  => (double) $this->discount,

            'discount_percentage'       => (double) $this->discount_percentage,
            'maximum_purchase_qty'  => $this->maximum_purchase_qty,
            'minimum_purchaseqty'   => $this->minimum_purchase_qty,
            'featured_image'   => show_image(file_path()['product']['featured']['path'].'/'.$this->featured_image),
            'gallery_image'    => $this->gallery->map(fn (ProductImage $image) :array => [
                "id" => $image->id ,
                "image" => show_image(file_path()['product']['gallery']['path'].'/'.$image->image)])->all(),

            'rating' => [
                'total_review' => count($this->review),
                'avg_rating'   =>  $this->review->avg('rating') ? ($this->review->avg('rating')) :0 ,
                'review'       => $reviews->all()
            ],

            'short_description'     => $this->short_description,
            'description'           => $this->description,
            'meta_title'            => $this->meta_title,
            'meta_keywords'         => $this->meta_keywords,
            'url'                   => Product::PUBLISHED == $this->status 
                                              ? route('product.details',[$this->slug ? $this->slug :  make_slug($this->name),$this->id])
                                              : null ,
            'created_at'            => get_date_time($this->created_at),
            'club_point'            => $this->point,
            'status'                => $status,
            'status_key'            => $this->status,
            'shippings'             => $this->shippingDelivery->pluck('shipping_delivery_id')->toArray(),
            'attributes'            => @json_decode($this->attributes,true) ?? [],
            'attribute_values'      => @json_decode($this->attributes_value,true) ?? [],
            'stock'                 => new StockCollection($this->stock),
            'digital_attributes'    => new DigitalAttributeCollection($this->digitalProductAttribute),

            'taxes' => new TaxCollection($this->taxes),
            'custom_information' => $this->custom_fileds

        ];
    }
}
