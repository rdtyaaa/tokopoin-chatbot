<?php

namespace App\Http\Resources\Seller;

use App\Http\Resources\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
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
           'id'              => $this->id,
           'trx_number'      => $this->trx_number,
           'method'          => new WithdrawMethodResource($this->method),
           'currency'        => new CurrencyResource($this->currency),
           'amount'          =>  (double)($this->amount),// default currency
           'charge'          =>  (double)($this->charge), // default currency
           'rate'            =>  (double)($this->rate), // default currency to withdraw currency
           'final_amount'    =>  (double)($this->final_amount), // in withdraw currency
           'status'          =>  ($this->status) ,
           'status_enum'     => [
                                    'Initiate'    => 0,
                                    'Success'     => 1,
                                    'Pending'     => 2,
                                    'Rejected'    => 3,
                              
                                ],
            'feedback'      => @$this->feedback,

            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),
            'created_at'                        => ($this->created_at),
       ];
    }
}
