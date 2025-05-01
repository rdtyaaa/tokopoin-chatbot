<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return  [

            "trx_id"            => $this->transaction_number,
            "amount"            => api_short_amount($this->amount),
            "post_balance"      => api_short_amount($this->post_balance),
            "transaction_type"  => $this->transaction_type,
            "details"           => $this->details,
            "created_at"        => ($this->created_at),
            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),

         ];
    }
}
