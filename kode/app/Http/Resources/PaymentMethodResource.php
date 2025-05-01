<?php

namespace App\Http\Resources;

use App\Models\PaymentMethod;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $paymentMethods = [
            'STRIPE101'      => 'stripe',
            'BKASH102'       => 'bkash',
            'PAYSTACK103'    => 'paystack',
            'NAGAD104'       => 'nagad',
            'PAYPAL102'      => 'paypal',
            'FLUTTERWAVE105' => 'flutterwave',
            'RAZORPAY106'    => 'razorpay',
            'INSTA106'       => 'instamojo',
            'MERCADO101'     => 'mercado',
            'PAYEER105'      => 'payeer',
            'AAMARPAY107'    => 'aamarpay',
            'PAYU101'        => 'payumoney',
            'PAYHERE101'     => 'payhere',
            'PAYKU108'       => 'payku',
            'PHONEPE102'     => 'phonepe',
            'SENANGPAY107'   => 'senangpay',
            'NGENIUS111'     => 'ngenius',
            'ESEWA107'       => 'esewa',
            'WEBXPAY109'       => 'webxpay',
        ];

        $method = Arr::get($paymentMethods , $this->unique_code); 

        $response = [
            'id'                   => (int) $this->id,
            'percent_charge'       => $this->percent_charge,
            'currency'             => $this->currency,
            'rate'                 => $this->rate,
            'name'                 => $this->name,
            'unique_code'          => $this->unique_code,
            'payment_parameter'    => ($this->payment_parameter),
            'is_manual'            => $this->type  == PaymentMethod::AUTOMATIC ? false : true ,
            'image'                => show_image(file_path()['payment_method']['path'].'/'.$this->image),
        ];

        if($this->type == PaymentMethod::AUTOMATIC)  $response ['callback_url'] =  route($method.".callback");
        
        return $response;


    }
}
