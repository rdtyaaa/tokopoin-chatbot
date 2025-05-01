<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'id'                    => $this->id,
            'name'                  => @$this->name,
            'email'                 => @$this->email,
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'phone'                 => $this->phone,
            'zip'                   => $this->zip,
            'state'                 => new StateResource($this->state),

            'city'                  => new CityResource($this->city),
      
            'address'               => $this->address,
            'country'               => new CountryResource($this->country)

        ];


    }
}
