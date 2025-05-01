<?php

namespace App\Http\Resources;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashDealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        

        $products =  Product::with(['gallery','review','order','stock','order','rating','shippingDelivery','shippingDelivery.shippingDelivery'])
                                ->whereIn('id',(array)@$this->products)
                                ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                ->get();
        return [
    
            'name'            => $this->name,
            'slug'            => $this->slug,
            'start_date'      => $this->start_date,
            'end_date'        => $this->end_date,
            'banner_image'    => show_image(file_path()['flash_deal']['path'].'/'.$this->banner_image),
            'products'        => new ProductCollection($products)
        ];
    }
}
