<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

      $displayName = $this->display_name ? $this->display_name : unslug($this->name);

      return [
        'id'    => $this->id,
        'uid'   => $this->uid,
        'name'  => $this->name,
        'display_name'  =>   $displayName,
      ];
    }
}
