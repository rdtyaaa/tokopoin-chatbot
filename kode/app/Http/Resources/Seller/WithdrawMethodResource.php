<?php

namespace App\Http\Resources\Seller;

use App\Http\Resources\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawMethodResource extends JsonResource
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
            'id'               => $this->id,
            'image'            => show_image(file_path()['withdraw']['path'].'/'.$this->image,file_path()['withdraw']['size']),
            'name'             => $this->name,
            'description'      => $this->description,
            'duration'         => (int) $this->duration,
            'duration_unit'    => "Hour",



            'min_limit'        => (double) $this->min_limit ?? 0,    //default currecny
            'max_limit'        => (double) $this->max_limit ?? 0,    //default currecny
            'fixed_charge'     => (double) $this->fixed_charge ?? 0, //default currecny
            'percent_charge'   => (double) $this->fixed_charge ?? 0, //default currecny
            
            
            'custom_inputs'    => collect($this->user_information)->map(fn(object $inputs) => [
                                                'name'     => $inputs->data_name,
                                                'type'     => $inputs->type,
                                                'label'    => $inputs->data_label,
                                                'required' => true])->values(),

            'currency'         => new CurrencyResource($this->currency),
      
        ];
    }
}
