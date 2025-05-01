<?php

namespace App\Http\Resources\Seller;

use App\Models\DigitalProductAttribute;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        $status = 'N/A';

        switch ($this->status) {
            case Order::PLACED:
                $status = "Placed";
                break;
            case Order::CONFIRMED:
                $status = "Confirmed";
                break;
            case Order::PROCESSING:
                $status = "Processing";
                break;
            case Order::SHIPPED:
                $status = "Shipped";
                break;
            case Order::DELIVERED:
                $status = "Delivered";
                break;
            case Order::CANCEL:
                $status = "Cancel";
                break;
            case Order::PENDING:
                $status = "Pending";
                break;

            case Order::RETURN:
                $status = "Returned";
                break;
        }

        $digitalProductAttribute = DigitalProductAttribute::where('id', $this->digital_product_attribute_id)->first();

        return [
            'id'                 => $this->id,
            'uid'                => $this->uid,
            'product_name'       => $this->product->name,
            'product_image'      => show_image(file_path()['product']['featured']['path'].'/'.$this->product->featured_image ,file_path()['product']['featured']['size']),
            'quantity'           => $this->quantity,
            'total_price'                 => api_short_amount($this->total_price),
            'original_total_price'        => api_short_amount($this->original_price),
            'total_taxes'        => api_short_amount($this->total_taxes),
            'discount'           => api_short_amount($this->discount),
            'attribute'          => ($this->attribute),
            'digital_attribute'  => $digitalProductAttribute ? $digitalProductAttribute->name : null,
            'status'             =>  $status,

        ];
    }
}
