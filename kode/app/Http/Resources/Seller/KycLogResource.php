<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class KycLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return  [  

            'id'                                => $this->id,
            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),
            'status'                            => $this->status,
            'feedback'                          => $this->feedback,
            'kyc_data'                          => collect($this->custom_data)->map(function( $data, string $key){
                                                        if($key == 'files') {
                                                            $data = collect($data)->map(function( $file){
                                                                return show_image(file_path()['seller_kyc']['path'] ."/".$file);
                                                            });
                                                            return $data->all();
                                                        }
                                                        return $data;
                                                    })->all(),

        ];
   

    }
}
