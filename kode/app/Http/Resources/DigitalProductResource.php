<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;

class DigitalProductResource extends JsonResource
{

    protected $digitalProductAttributeId;

    public function __construct($resource, $digitalProductAttributeId =   null )
    {
        parent::__construct($resource);
        $this->digitalProductAttributeId = $digitalProductAttributeId;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $attribute_options =  [];

        foreach ($this->digitalProductAttribute as $attribute) {

            $price      = @$attribute->price ?? 0;
            $taxes      = getTaxes(@$this,$price);
            $price      =  $price  + $taxes;

            if ($this->digitalProductAttributeId == $attribute->id) {
                $attribute_options[$attribute->name] = [
                    'uid'           => $attribute->uid,
                    'price'         => api_short_amount((double)$price),
                    'price_without_tax'         => api_short_amount((double)@$attribute->price),
                    'short_details' => $attribute->short_details,
                    'product_id'    => $attribute->product_id,
                ];
                break;
            } elseif ($attribute->status == '1') {
                $attribute_options[$attribute->name] = [
                    'uid' => $attribute->uid,
                    'price' => api_short_amount((double)$price),
                    'price_without_tax'         => api_short_amount((double)@$attribute->price),
                    'short_details' => $attribute->short_details,
                    'product_id' => $attribute->product_id,
                ];
            }
        }
   
        $price =  0;

        if($this->digitalProductAttribute->count() > 0){

            $attribute =  $this->digitalProductAttribute()
                                ->where('status','1')
                                ->when($this->digitalProductAttributeId,fn(Builder $query) : Builder =>
                                    $query->where('id',$this->digitalProductAttributeId)
                                )->first();

            if($attribute && $attribute->price)  {
                $price      = @$attribute->price ?? 0;
                $taxes      = getTaxes(@$this,$price);
                $price      = api_short_amount($price+$taxes);
            }


        }
       
        return [
            
            'uid'                => $this->uid,
            'name'               => $this->name,
            'attribute'          => (object)$attribute_options ,
            'price'              => $price,
            'short_description'  => $this->short_description,
            'description'        => $this->description,
            'featured_image'     => show_image(file_path()['product']['featured']['path'].'/'.$this->featured_image),
            'url'                => route('digital.product.details', [$this->slug ? $this->slug : make_slug($this->name), $this->id]),
            'seller'             => new SellerResource($this->seller),
            'taxes'              => new TaxCollection($this->taxes),
            'club_point'         => $this->point,
            'custom_information' => $this->custom_fileds

        ];
    }
}
