<?php

namespace App\Http\Resources;

use App\Http\Resources\Deliveryman\DeliveryManOrderResource;
use App\Http\Resources\Deliveryman\DeliveryManResource;
use App\Models\DeliveryMan;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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

        $paymentMethod  = $this->paymentMethod ? $this->paymentMethod->name : null;

        $data =  [
            'id'                  => $this->id,
            'uid'                 => $this->uid,
            'order_id'            => $this->order_id,
            'order_date'          => $this->created_at,
            'quantity'            => $this->qty,
            'shipping_charge'     => api_short_amount((double)$this->shipping_charge,2),
            'discount'            => api_short_amount((double)$this->discount,2),
            'amount'              => api_short_amount((double)$this->amount,2),
            'original_amount'     => api_short_amount((double)$this->original_amount,2),
            'total_taxes'         => api_short_amount((double)$this->total_taxes,2),

            'wallet_payment'      => $this->wallet_payment == Order::WALLET_PAYMENT ? true : false,

            'payment_type'        => $this->payment_type == '1' ? 'Cash On Delivary' : $paymentMethod,
            'payment_status'      => $this->payment_status == '1' ? 'Unpaid' :"Paid",
            'shipping_method'     => $this->shipping ? $this->shipping->name : null,
            'status'              => $status,
            'status_log'          => OrderStatusResource::collection($this->orderStatus->whereNotNull('delivery_status')),
            'order_details'       => count($this->orderDetails)!= 0 ?  new TransactionDetailsCollection($this->orderDetails) : [],
            'billing_address'     => collect(@$this->billing_information),
            'payment_details'     => $this->payment_details,
            'custom_information'  => $this->custom_information

        ];
        if($this->relationLoaded('deliveryManOrder') && @$this->deliveryManOrder ){
            $deliveryMan = @$this->deliveryManOrder->deliveryMan;
            if(@$deliveryMan) {
                $data['delivery_man'] = new DeliveryManResource(@$deliveryMan);
                $data['order_delivery_info']  = new DeliveryManOrderResource($this->deliveryManOrder);
            }
        }
        if($this->relationLoaded('orderRatings') && @$this->orderRatings ){
            $data['order_ratings'] = new DeliveryManRatingCollection(@$this->orderRatings);
        }

        if($this->relationLoaded('billingAddress') && @$this->billingAddress && @$this->address_id){
            $data['new_billing_address'] = new AddressResource(@$this->billingAddress);
        }

        if(@$this->shipping){
            $data['shipping_info'] = [
                'name'             => @$this->shipping->name ?? 'N/A',
                'duration'         => @$this->shipping->duration ?? 'N/A',
                'duration_unit'    => 'Days',
            ];
        }

        return $data;


    }
}
