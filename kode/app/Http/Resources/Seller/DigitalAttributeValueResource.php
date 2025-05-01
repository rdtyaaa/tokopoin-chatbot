<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class DigitalAttributeValueResource extends JsonResource
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

             "id"    => $this->id,
             "uid"   => $this->uid,
             "name" => $this->name,
             "value" => $this->value,
             "file" => $this->file ? show_image(file_path()['product']['attribute']['path'].'/'.$this->file) : null,
             "created_at" => diff_for_humans($this->created_at),
             "status" => $this->status == '1' ? "Active" : "Inactive",
             "status_key" => $this->status ,

        ];
      
    }
}
