<?php
namespace App\Http\Utility;
use App\Models\ProductImage;

class ProductGallery
{
    public static function imageStore($request, $galleryImage, $productId)
    {
        foreach($galleryImage as $optional){
            $product = new ProductImage();
            $product->product_id = $productId;
            try{
                $galleryImage = store_file($optional,file_path()['product']['gallery']['path']);
            }catch (\Exception $exp) {
              
            }
            $product->image = $galleryImage;
            $product->save();
        }
    }
}