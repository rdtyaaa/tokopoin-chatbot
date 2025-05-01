<?php

namespace App\Http\Resources;

use App\Models\AttributeValue;
use App\Models\DigitalProductAttributeValue;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailsResource extends JsonResource
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


        


        $digital_attributes = null;


        if($this->digital_product_attribute_id){

            $orderDetails = $this->load(['digitalProductAttributeValue','digitalProductAttributeValue.digitalProductAttributeValueKey']);

            $digital_attributes = $orderDetails->digitalProductAttributeValue->digitalProductAttributeValueKey->where('status',1)->map(fn (DigitalProductAttributeValue $value): array  => [
                    'name'  => $value->name ? $value->name : "N/A",
                    'value' => $value->value ,
                    'file'  => $value->file ?  show_image(file_path()['product']['attribute']['path'].'/'.$value->file) : null]
            )->all();


        }

 
        return [
            'uid'         => $this->uid,
            'product'     => $this->digital_product_attribute_id != null 
                                     ? new DigitalProductResource($this->product ,$this->digital_product_attribute_id ) 
                                     : new ProductResource($this->product),
            
            'order_id'    => $this->order_id,
            'quantity'    => $this->quantity,
            'total_price' => api_short_amount($this->total_price,2),
            'original_total_price'        => api_short_amount($this->original_price),
            'total_taxes'        => api_short_amount($this->total_taxes),
            'discount'        => api_short_amount($this->discount),

            'attribute'   => $this->attribute 
                                   ? $this->attribute  
                                   : null,
             
            'digital_attributes'   => @$digital_attributes,
             
            'status'      => $status
        ];
    }
}
