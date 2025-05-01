<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
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
            'tax_name' => $this->name,
            'amount'   => $this->pivot?->amount,
            'type'     => $this->pivot->type == 0 ? "percentage" :"flat",
        ];
    }
}
