<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
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
            "id"                => $this->id,
            "created_at"        => get_date_time($this->created_at),
            "message"           => ($this->message),
            "is_admin_reply"    => ($this->admin_id ? true :false),
            "is_user_reply"     => (!$this->admin_id ? true :false),
            'files'             => $this->supportfiles
        ];
    }
}
