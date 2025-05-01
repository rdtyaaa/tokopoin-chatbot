<?php

namespace App\Http\Resources\Seller;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class OrderStatusResource extends JsonResource
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

        switch ($this->delivery_status) {
            case Order::PENDING:
                $status = "Pending";
                break;
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
            case Order::RETURN:
                $status = "Returned";
                break;
        }



        return [
                'id'               => $this->id,
                'uid'              => $this->uid,
                'payment_status'   => $this->payment_status == 1 ? 'Unpaid' :'paid',
                'payment_note'     => $this->payment_note,
                'delivery_status'  => $status ,
                'delivery_status_key'  => Arr::get(array_flip(Order::delevaryStatus()),$this->delivery_status) ,
                'delivery_note'    => $this->delivery_note,
                'created_at'       => diff_for_humans($this->created_at),
        ];
    }
}
