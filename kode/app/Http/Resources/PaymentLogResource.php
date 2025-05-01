<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

          return [
            "payment_method"   => $this->paymentGateway ? new PaymentMethodResource($this->paymentGateway) : null,
            'uid'              => $this->uid,
            'trx_number'       => $this->trx_number,
            'amount'           => show_amount((double)$this->amount, default_currency()->symbol),
            'charge'           => show_amount((double)$this->charge, default_currency()->symbol),
            'payable'          => show_amount( (double) $this->final_amount, $this->paymentGateway->currency->symbol ?? default_currency()->symbol ),
            'exchange_rate'    => round($this->rate,2),
            'final_amount'     => round($this->final_amount,2),
            'status'           => $this->status,
            'type'             => $this->type,
            'feedback'         => $this->feedback,
            'custom_info'      => $this->custom_info,
            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),
        ];
    }
}
