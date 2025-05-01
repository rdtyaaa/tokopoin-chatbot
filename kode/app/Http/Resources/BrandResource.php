<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'uid'  => $this->uid,
            'name' => json_decode($this->name, true),
            'slug'            => $this->slug,
            'logo' => show_image(file_path()['brand']['path'].'/'.$this->logo),
        ];
    }
}
