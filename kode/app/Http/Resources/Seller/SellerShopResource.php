<?php

namespace App\Http\Resources\Seller;

use App\Enums\StatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerShopResource extends JsonResource
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
            'name'                  => $this->name,
            'short_details'         => $this->short_details,
            'email'                 => $this->email,
            'phone'                 => $this->phone,
            'address'               => $this->address,
            'whatsapp_number'       => $this->whatsapp_number,
            'whatsapp_order'        => $this->whatsapp_order ,
            'is_shop_active'        => $this->status == 1 ? true : false,

            'url'                  => $this->status == 1 ? route('seller.store.visit',[make_slug($this->name), $this->id]) : null,
            
            'shop_logo'             => [
                                         'url' => show_image(file_path()['shop_logo']['path'].'/'.$this->shop_logo, file_path()['shop_logo']['size']),
                                         'size_guide' => file_path()['shop_logo']['size']
                                       ],
            
            'shop_feature_image'    => [ 
                                            'url' => show_image(file_path()['shop_first_image']['path'].'/'.@$this->shop_first_image ,file_path()['shop_first_image']['size']),
                                            'size_guide' => file_path()['shop_first_image']['size']
                                        ],

            'site_logo'             =>  [ 
                                            'url' =>show_image(file_path()['seller_site_logo']['path'].'/'.@$this->seller_site_logo,file_path()['seller_site_logo']['size']),
                                            'size_guide' => file_path()['seller_site_logo']['size']
                                        ], 
                                        
            
            
            'site_logo_icon'        => [ 
                                            'url' =>show_image(file_path()['seller_site_logo']['path'].'/'.@$this->logoicon,file_path()['loder_logo']['size']),
                                            'size_guide' => file_path()['loder_logo']['size']
                                        ]
            
 

        ];
    }
}
