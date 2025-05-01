<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'name'              => $this->name,
            'image'             => show_image(file_path()['campaign_banner']['path'].'/'.$this->banner_image),
            'start_time'        => $this->start_time,
            'end_time'          => $this->end_time,
            'discount'          => $this->discount,
            'discount_type'     => $this->discount_type == 1 ? 'percentage' : 'fixed',
            'products'          => new ProductCollection($this->products)
        ];
    }
}
