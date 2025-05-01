<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'ticket_number' => (string) $this->ticket_number,
            'name'          => $this->name,
            'email'         => $this->email,
            'subject'       => $this->subject,
            'created_at'    => get_date_time($this->created_at),
            'priority_enum' => [
                'low'    => 1,
                'medium' => 2,
                'high'   => 3,
            ],
            'priority'      => $this->priority,
 
            'status_enum'    => [
                'Running'    => 1,
                'Answered'   => 2,
                'Replied'    => 3,
                'closed'     => 4,
            ],
            'status'        => $this->status,
        ];
    }
}
