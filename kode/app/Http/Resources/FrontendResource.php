<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FrontendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $section_data = json_decode($this->value,true);
        if(isset($section_data['image'])){

            $image_info            = $section_data['image'];
            $image_info['value']   = show_image(file_path()['frontend']['path'].'/'. $image_info['value'] );
            $section_data['image'] = $image_info;
        }
        if(isset($section_data['image_2'])){

            $image_info              = $section_data['image_2'];
            $image_info['value']     = show_image(file_path()['frontend']['path'].'/'. $image_info['value'] );
            unset($section_data['image_2']);
            $section_data['image_1'] = $image_info;
        }


        return [
            'slug'  => $this->slug,
            'name'  => $this->name,
            'value' => $section_data
        ];
        
     
    }
}
