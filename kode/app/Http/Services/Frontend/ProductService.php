<?php

namespace App\Http\Services\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CampaignProduct;
use App\Models\Cart;
use App\Models\CompareProductList;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\ProductStock;
use App\Models\User;
use App\Models\WishList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ProductService extends Controller
{



  /** product stock price */
  public function productStock($request){
   
    $discount_price = 0;
    $product_campaign = null;
    if(request()->campaign_id){
        $product_campaign = CampaignProduct::where("product_id",$request->id)->where('campaign_id',$request->campaign_id)->first();
    }
    $atrribute = (implode("-",$request->attribute_id));



    $product = Product::where('id',$request->id)->first();



    $stock =  ProductStock::where('product_id',$request->id)
                            ->where("attribute_value",$atrribute)
                            ->first();

    $price      = $stock->price;

    if($product_campaign){
      $discount_price = (short_amount(discount($price,$product_campaign->discount,$product_campaign->discount_type),false));
    }
    else{
        if(($product->discount_percentage) > 0){
          $discount_price = (short_amount(cal_discount($product->discount_percentage, $price) ,false));
          $price          =  short_amount($price,false);
        }
    }



    return json_encode([
        'price' => $price,
        'discount_price' => $discount_price,
        'stock'  => $stock->qty  > 0  ? true : false
    ]);

  }

  /** add to cart method */
  public function addToCart($request){

    
    $response = [];
    $userId = auth_user('web') ? auth_user('web')->id : null;
    $sessionId = session()->has('session_id') ? session()->get('session_id') :null ;
    $product = Product::with(['stock'])->where('id',$request->id)->first();

    $atrribute = @$product->stock->where('qty','>',0)->first()->attribute_value;


    if($request->attribute_id)  $atrribute = (implode("-",$request->attribute_id));
   
    $stock =  ProductStock::where('product_id',$request->id)->where("attribute_value",$atrribute)->first();
    $quantity = 1;
    if($request->quantity) $quantity = $request->quantity;

    if($quantity > $product->maximum_purchase_qty) return (['error' => 'The maximum should be '.$product->maximum_purchase_qty.' product purchase']);

    if(!$stock || $quantity > $stock->qty) return (['error' => 'Stock Not Available']);

        $price      = ($stock->price);
        $taxeCollection             =  getTaxesCollection(@$product,$price);
        $taxes                      =  getTaxes(@$product,$price);

        $discount                   =  0;
      
    
        if(request()->campaign_id) {
            $productCampaign = CampaignProduct::where('product_id', $request->id)
                                        ->where('campaign_id', $request->campaign_id)
                                        ->first();

        }
        if (@$productCampaign) {
                $prevPrice = $price;
                $price     = (discount($price, $productCampaign->discount, $productCampaign->discount_type));
                $discount  =  $prevPrice -     $price;
            
        }else {
            if ($product->discount_percentage > 0) {
                $prevPrice = $price;
                $price     = (cal_discount($product->discount_percentage, $price));
                $discount  =  $prevPrice -     $price;
            }
        }
      
      if($sessionId == null) {
          session()->put('session_id', random_number());
          $sessionId = session()->get('session_id');
      }
      $cartQuery = Cart::where('product_id', $request->id)->where('attributes_value', $atrribute);
      if ($userId) {
          $cartQuery->where('user_id', $userId);
      }
      if($sessionId){
        $cartQuery->whereNotNull('session_id')->where('session_id', $sessionId);
      }
      $cart = $cartQuery->first();
    if ($cart) {

      if($request->input("checkout") && $request->input("checkout") == 1)return (['success' => translate("Product Added To Cart")]);

        if (($request->campaign_id == $cart->campaign_id || $cart->user_id == $userId) && $cart->attributes_value == $atrribute ) {
            $quantity =  $cart->quantity + $quantity ;
            if( $quantity > $product->maximum_purchase_qty || $quantity >$stock->qty  ) return (['error' => 'Already Added!! & Maximum product purchase Quantity exceed  ']);

            $cart->quantity = $quantity;
            $cart->total =  ($cart->price*$quantity) ;

        }
        else{


            if($request->campaign_id){
                $taxeCollection             =  getTaxesCollection(@$product,$price);
                $taxes                      =  getTaxes(@$product,$price);
                $price                      =  $price + $taxes;
                $priceWithTax               =  $stock->price + $taxes ;
                Cart::create([
                  'campaign_id'     => $request->campaign_id ?? $cart->campaign_id,
                  'user_id'         => $userId,
                  'session_id'      => $sessionId,
                  'discount'        => $discount,
                  'product_id'      => $product->id,
                  'price'           => $price ,
                  'original_price'  => $priceWithTax,
                  'total'           => ($price*$quantity) ,
                  'quantity'        => $quantity ,
                  'taxes'           => $taxeCollection,
                  "total_taxes"     => $taxes,
                  'attribute'       => $request->attribute_id ?? null,
                  'attributes_value'=> $atrribute
              ]);

              return (['success' => translate("Product Added To Cart")]);
            }
            
        }
        
        $cart->save();

        return (['success' => translate("Cart Quantity Updated")]);
        
    } else {
      
        $taxeCollection             =  getTaxesCollection(@$product,$price);
        $taxes                      =  getTaxes(@$product,$price);
        $price                      =  $price + $taxes;
        $priceWithTax               =  $stock->price + $taxes ;
        Cart::create([
            'campaign_id'    => $request->campaign_id,
            'user_id'        => $userId,
            'session_id'     => $sessionId,
            'product_id'     => $product->id,
            'original_price' => $priceWithTax,
            'discount'       => $discount,
            'price'          => $price,
            'total'          => ($price*$quantity) ,
            'quantity'       => $quantity,
            'attribute'      => $request->attribute_id,
            'taxes'          => $taxeCollection,
            "total_taxes"    =>  $taxes,
            'attributes_value'=> $atrribute
        ]);
    }
    return (['success' => translate("Product Added To Cart")]);


    
  }



 
  /**
   * Delete a specific cart item 
   *
   * @param integer $id
   * @param User|null $user
   * @return boolean
   */
  public function deleteCartItem(int $id ,? User $user = null) : bool{

    $cartItem = Cart::when($user,fn(Builder $query) => $query->where('user_id',$user->id))
                      ->where('id',$id)->first();
                   
                                    

    if($cartItem){
        $cartItem->delete();
        return true;
    }

    return false;
   
  }

  /**update cart qty */
  public function updateCartItem($request){


    $cartItem = Cart::find($request->id);
    if(!$cartItem) return response()->json(['error' => 'Cart Item not found']);

    if($request->quantity == 0){
       $cartItem->delete();
       return response()->json([
        'success' => 'Item removed form cart',
        'reload'  => true,
      ]);
    }

    if($cartItem->product->minimum_purchase_qty > $request->quantity){
        return response()->json(['error' => 'The minimum should be '.$cartItem->product->minimum_purchase_qty.' product purchase']);
    }
    if($request->quantity > $cartItem->product->maximum_purchase_qty){
        return response()->json(['error' => 'The maximum should be '.$cartItem->product->maximum_purchase_qty.' product purchase']);
    }
    $cartItem->quantity = $request->quantity;
    $cartItem->total = $cartItem->price*$request->quantity;
    $cartItem->save();
    $coupon = false;
    if(session()->has('coupon')){
        $coupon = true;
        session()->forget('coupon');
    }
    return response()->json([
        'coupon' => $coupon,
        'success' => 'Cart item qty has been updated'
    ]);
  }

  /**update cart after login */
  public function updateCart($authUser){
    $sessionId = session()->get('session_id');
    if($sessionId){
      Cart::where('session_id',$sessionId)->update(['user_Id'=>$authUser->id]);
    }

   
  }



  
  /**
   * Add or remove a product in wishlist
   *
   * @param Product $request
   * @param User|null $user
   * @return array
   */
  public function wishList(Product $product, ? User $user = null): array{
    
    $wishlist = WishList::where('customer_id',$user->id)
                          ->where('product_id', $product->id)
                          ->first();
      
    if($wishlist){
        $wishlist->delete(); 
        return ['status' => true , "is_removed" => true ,'message' => translate("Item removed form wishlist") ];
    }

    WishList::create([
        'customer_id' => $user->id,
        'product_id'  => $product->id
    ]);
    
    return ['status' => true , "is_removed" => false ,'message' => translate("Item has been added to wishlist")];

  }




  /**wish list items count  */
  public function wishListItems(){
      $wishlistItemCount = 0;
      if(auth_user('web')){
        $wishlistItemCount = Wishlist::where('customer_id', auth_user('web')->id)->count();
      }
      return $wishlistItemCount;
  }

  /**wish list items delete  */
  public function wishListItemsDelete($request){
      $wishlist = Wishlist::findorFail($request->id);
      $wishlist->delete();
      return response()->json(['success' => 'Wishlist item has been deleted']);
  }

  /**compare items count  */
  public function compareCount(){
      $user = auth_user('web');
      $compareItemCount = 0;
      if($user){
        $compareItemCount = CompareProductList::where('customer_id', $user->id)->count();
      }
      return $compareItemCount;
  }

  /**add to compare lsit  */
  public function addToCompare($request){
        $product = Product::where('id', $request->product_id)->first();
        if(!$product){
            return response()->json(['error' => "Product doesn't exist"]);
        }
        $user =auth_user('web');
        $compareProduct = CompareProductList::where('customer_id', $user->id)->pluck('product_id')->toArray();
        if(in_array($request->product_id, $compareProduct)){
            return response()->json(['error' => 'This item has been already added to compare list.']);
        }
        if(count($compareProduct) >= 4){
            return response()->json(['error' => 'At a time only Four items compare on the list']);
        }else{
            $compareProductList = new CompareProductList();
            $compareProductList->customer_id = $user->id;
            $compareProductList->product_id = $product->id;
            $compareProductList->save();
        }
        return response()->json(['message' => 'Item has been added to compare list']);
  }

  /**get all cart data */
  public function getCart(? User $user = null){
    
      $subtotal = 0;
      $response = [];
      $latestItem = $this->getCartItem($user);
      if($latestItem->isNotEmpty()){
          foreach($latestItem as $item){
              $subtotal += ($item->price - $item->total_taxes) * $item->quantity;
          }
      }
      $response ['latest_item'] = $latestItem;
      $response ['sub_total'] = $subtotal;
      return $response;
      
  }




  

  /**
   * Get cart items
   *
   * @param User|null $user
   * @return Collection|null
   */
  public function getCartItem(? User $user) : ? Collection {
 
        return Cart::with(['campaigns'])
                    ->orderBy('id', 'desc')
                    ->with(['product','product.review'])
                    ->when($user,fn(Builder $query) => $query->where('user_id',$user->id))
                    ->when(!$user,fn(Builder $query):Builder =>  
                        $query->whereNotNull('session_id')
                                        ->where('session_id', session()->get('session_id'))
                    )
                    ->get();

  }





  /**
   * Get a specific product reviews
   *
   * @param Request $request
   * @return LengthAwarePaginator
   */
  public function getReviews(Request $request) :LengthAwarePaginator {

        return  ProductRating::with(['customer'])
                    ->where('product_id',$request->input('id'))
                    ->paginate(paginate_number(), ['*'], 'page', $request->input('page'));
  }

}