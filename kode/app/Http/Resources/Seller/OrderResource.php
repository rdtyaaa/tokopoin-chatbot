<?php

namespace App\Http\Resources\Seller;

use App\Http\Resources\AddressResource;
use App\Http\Resources\Deliveryman\DeliveryManOrderResource;
use App\Http\Resources\UserResource;
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


        $paymentType = '';

        switch ($this->payment_type) {
            case Order::COD:
                $paymentType = "Cash on delivery";
                break;
            case Order::PAYMENT_METHOD:
                $paymentType = @$this->paymentMethod->name;
                break;

        }

       $totalAmount    = @$this->orderDetails?->sum('total_price') ?? 0;

       $paymentDetails = collect($this->payment_details) ;

       $invoiceLogos = json_decode(site_settings('invoice_logo'),true);

       $invoiceLogo =  null;


       switch ($this->payment_type) {
        case Order::COD:
              $invoiceLogo = asset('assets/images/backend/invoiceLogo/'.$invoiceLogos['Cash On Delivery']);
            break;
        case Order::PAYMENT_METHOD:
               switch ($this->payment_status) {
                    case Order::PAID:
                            $invoiceLogo = asset('assets/images/backend/invoiceLogo/'.$invoiceLogos['paid']);
                        break;
                    
                    default:
                            $invoiceLogo = asset('assets/images/backend/invoiceLogo/'.$invoiceLogos['unpaid']);
                        break;
               }
            $invoiceLogo = @$this->paymentMethod->name;
            break;
        default:
            $invoiceLogo =  null;
            break;

      }
    
        $data =  [  

            'id'                                => $this->id,
            'uid'                               => $this->uid,
            'verification_code'                 => $this->verification_code,
            'type'                              => $this->order_type == Order::DIGITAL ? "Digital" : "Physical" ,
            'type_flag'                         => $this->order_type ,
            'order_id'                          => $this->order_id,
            'quantity'                          => $this->qty,
            'wallet_payment'                    => $this->wallet_payment == Order::WALLET_PAYMENT ? true : false,
            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),
            'customer_info'                     => new UserResource($this->customer),
            'billing_address'                   => collect(@$this->billing_information),                        
            'payment_details'                   => $paymentDetails,
            'total_product'                      => @$this->orderDetails?->count() ?? 0 ,
            'order_amount'                       => api_short_amount(@$totalAmount ?? $this->amount),
            'original_amount'                    => api_short_amount((double)$this->original_amount,2),
            'total_taxes'                        => api_short_amount((double)$this->total_taxes,2),
            'shipping_charge'                    => api_short_amount(@$this->shipping_charge ?? 0),
            'payment_status'                     => $this->payment_status == Order::UNPAID
                                                            ? translate('Unpaid')
                                                            : translate('Paid'),

            'invoice_logo'                       => $invoiceLogo,
            'delevary_status'                    => $status,
            'delevary_status_key'                 => $this->status,
            'payment_via'                        => $paymentType ,


            "order_details"        => new OrderDetailsCollection($this->orderDetails),
            "order_status"         => new OrderStatusCollection($this->orderStatus),
            'discount'             => api_short_amount((double)$this->discount,2),
            'custom_information'   => $this->custom_information,
            'order_delivery_info'  => new DeliveryManOrderResource($this->deliveryManOrder) 
        
        ];

        if(@$this->shipping){
            $data['shipping_info'] = [
                'name'             => @$this->shipping->name ?? 'N/A',
                'duration'         => @$this->shipping->duration ?? 'N/A',
                'duration_unit'    => 'Days',
            ];
        }

        if($this->relationLoaded('billingAddress') && @$this->billingAddress && @$this->address_id ){
            $data['new_billing_address'] = new AddressResource(@$this->billingAddress);
        }
        return $data;
    }
}
