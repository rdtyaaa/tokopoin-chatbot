<?php

namespace App\Http\Resources;

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
            'uid'          => $this->uid,
            'name'         => $this->name,
            'image'        => show_image(file_path()['campaign_banner']['path'].'/'.$this->banner_image),
            'start_time'   => date("Y-m-d H:i:s",strtotime($this->start_time)),
            'end_time'     => date("Y-m-d H:i:s",strtotime($this->end_time)),
        ];
    }
}
