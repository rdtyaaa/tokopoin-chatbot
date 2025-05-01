<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeCategoryResource extends JsonResource
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
            'title'                    => $this->title,
            'category'                 => new CategoryResource($this->category),
            'products'                 => new ProductCollection($this->category->product->where('product_type', Product::PHYSICAL)->take(10)),
        ];
    
    }
}
