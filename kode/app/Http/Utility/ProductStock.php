<?php
namespace App\Http\Utility;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Cache;

class ProductStock
{
    public static function stock($request, $attribute, $productId)
    {
        foreach($request->attribute_id as $key => $value){
            foreach($request->attributevalue[$key] as $okey => $stockValue){
                ProductStock::create([
                    'product_id' => $product->id,
                    'attribute_id' => $request->attribute_id[$key],
                    'attribute_value' => $request->attributevalue[$key][$okey],
                    'stock' => $request->attributestock[$key][$okey],
                ]);
            }
        }
    }
}