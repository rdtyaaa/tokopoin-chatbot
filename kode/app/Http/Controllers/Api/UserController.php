<?php

namespace App\Http\Controllers\api;
use App\Enums\RewardPointStatus;
use App\Enums\Settings\CacheKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartCollection;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentLogResource;
use App\Http\Resources\RewardPointCollection;
use App\Http\Resources\Seller\TransactionCollection;
use App\Http\Resources\TicketMessageResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WishlistCollection;
use App\Http\Utility\PaymentInsert;
use App\Http\Services\Frontend\CheckoutService;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\DigitalProductAttribute;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ShippingDelivery;
use App\Models\SupportFile;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\WishList;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TicketCollection;
use App\Jobs\SendMailJob;
use App\Models\City;
use App\Models\CountryZone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\DeliveryMan;
use App\Models\DeliveryManRating;
use App\Models\RewardPointLog;
use App\Models\UserAddress;
use App\Models\Transaction;
class UserController extends Controller
{

    protected CheckoutService $checkoutService;
    protected ? User $user;
    public function __construct()
    {
        $this->checkoutService = new CheckoutService();

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('api')->user()?->load(['country','billingAddress']);
            return $next($request);
        });
    }

    /**
     * Get all dashboard data
     *
     * @return JsonResponse
     */
    public function dashboard() : JsonResponse
    {
        $user            = $this->user;

        $wishlists       = Wishlist::with(['product'])
                                    ->latest()
                                    ->where('customer_id', $user->id)
                                    ->paginate(site_settings('pagination_number',10));

        $carts           = Cart::with(['product'])
                                    ->latest()
                                    ->where('user_id', $user->id)
                                    ->paginate(site_settings('pagination_number',10));
        $orders          = Order::with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user',])
                                    ->latest()
                                    ->physicalOrder()->where('customer_id',$user->id)
                                    ->paginate(site_settings('pagination_number',10));
        $digital_orders  = Order::with(['orderDetails','orderDetails.product'])
                                    ->digitalOrder()
                                    ->latest()
                                    ->where('customer_id',$user->id)
                                    ->paginate(site_settings('pagination_number',10));


        return api([
            'wishlists'      => new WishlistCollection($wishlists),
            'carts'          => new CartCollection($carts),
            'user'           => new UserResource($user),
            'orders'         => new OrderCollection($orders),
            'digital_orders' => new OrderCollection($digital_orders),
        ])->success(__('response.success'));
    }



    public function addressStore(Request $request) : JsonResponse{

        $rules = [
            'address_name' => 'required',
            'first_name'   => 'required|max:255',
            'last_name'    => 'required|max:255',
            'email'        => 'required|email',
            'phone'        => 'required',
            'address'      => 'required',
            'zip'          => 'required',
            'city_id'       => 'required|numeric|exists:cities,id',
            'state_id'      => 'required|numeric|exists:states,id',
            'country_id'    => 'required|numeric|exists:countries,id',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
        ];


        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $user            = $this->user;


        $address = [
                'address' => $request->input('address'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude')
        ];

        UserAddress::create([
            'name'             => $request->input('address_name','default') ,
            'email'            => $request->input('email') ,
            'user_id'          => $this->user->id,
            'first_name'       => $request->input('first_name'),
            'last_name'        => $request->input('last_name'),
            'phone'            => $request->input('phone'),
            'zip'              => $request->input('zip'),
            'country_id'       => $request->input('country_id'),
            'state_id'         => $request->input('state_id'),
            'city_id'          => $request->input('city_id'),
            'address'          => $address
        ]);

        $user            = $this->user->load(['country','billingAddress']);

       return api([
        'user'           => new UserResource($user),
       ])->success(__('response.success'));

    }



    public function addressUpdate(Request $request) : JsonResponse{

        $rules = [
            'id'          => ['required','exists:user_addresses,id'],
            'address_name' => 'required',
            'first_name'   => 'required|max:255',
            'last_name'    => 'required|max:255',
            'email'        => 'required|email',
            'phone'        => 'required',
            'address'      => 'required',
            'zip'          => 'required',
            'city_id'       => 'required|numeric|exists:cities,id',
            'state_id'      => 'required|numeric|exists:states,id',
            'country_id'    => 'required|numeric|exists:countries,id',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',

        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


       $user            = $this->user;

       $userAddress = UserAddress::where('id',$request->id)->where('user_id',$user->id)->firstOrfail();

       $address = [
           'address' => $request->input('address'),
           'latitude' => $request->input('latitude'),
           'longitude' => $request->input('longitude')
       ];

       $userAddress->name = $request->input('address_name','default');
       $userAddress->email = $request->input('email');
       $userAddress->first_name = $request->input('first_name');
       $userAddress->last_name = $request->input('last_name');
       $userAddress->zip = $request->input('zip');
       $userAddress->country_id = $request->input('country_id');
       $userAddress->city_id = $request->input('city_id');
       $userAddress->state_id = $request->input('state_id');
       $userAddress->address =  $address;
       $userAddress->save();
       $user            = $this->user->load(['country','billingAddress']);
       return api([
        'user'           => new UserResource($user),
       ])->success(__('response.success'));


    }


    public function addressDelete($id) : JsonResponse{

        $userAddress = UserAddress::withCount(['orders'])->where('id',$id)
                            ->where('user_id',$this->user->id)
                            ->firstOrfail();

        if($userAddress->orders_count == 0){
            $userAddress->delete();
            $user            = $this->user->load(['country','billingAddress']);
            return api([
                'user'           => new UserResource($user),
               ])->success(__('response.success'));
        }

        return api(['errors'=>[translate('This address has orders under it')]])->fails(__('response.fail'));

    }


    /**
     * Get all cart data
     *
     * @return JsonResponse
     */
    public function cart() :JsonResponse {

        $user  = $this->user;
        $carts = Cart::with(['product'])
                        ->where('user_id', $user->id)
                        ->paginate(site_settings('pagination_number',10));

        return api([
            'carts'  => new CartCollection($carts),
            'user'   => new UserResource($user),
        ])->success(__('response.success'));

    }


    /**
     * Get all cart data
     *
     * @return JsonResponse
     */
    public function transactions() :JsonResponse {

        $user  = $this->user;
        $transactions = Transaction::where('user_id', $user->id)
                                    ->latest()
                                    ->search()
                                    ->date()
                                    ->paginate(site_settings('pagination_number',10))
                                    ->appends(request()->all());

        return api([
            "transactions" =>  new TransactionCollection(  $transactions )

        ])->success(__('response.success'));

    }



    /**
     * Get all wishlist data
     *
     * @return JsonResponse
     */
    public function wishlistItem() :JsonResponse  {

        $user      = $this->user;
        $wishlists = Wishlist::with(['product'])->where('customer_id', $user->id)->paginate(site_settings('pagination_number',10));

        return api([
            'wishlists' => new WishlistCollection($wishlists),
            'user'      => new UserResource($user),
        ])->success(__('response.success'));

    }


    /**
     * Add to cart
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addtocart(Request $request) :JsonResponse {

        $validator = Validator::make($request->all(), [
            'product_uid' => 'required',
        ]);

        if ($validator->fails()) return api(['errors'=> $validator->errors()->all()])->fails('Validation Error');

        $userId       = $this->user->id;
        $campaign     = null;
        $product      = Product::with(['stock'])->where('uid',$request->product_uid)->first();

        if(!$product)  return api(['errors' => ['Product Not found']])->fails(__('response.fail'));

        $atrribute    = @$product->stock->first()->attribute_value;

        if($request->attribute_combination) $atrribute = $request->attribute_combination;

        $stock =  ProductStock::where('product_id',$product->id)
                                     ->where("attribute_value",$atrribute)
                                     ->first();

        $quantity = $request->quantity ??  1;

        if($quantity > $product->maximum_purchase_qty) return api(['errors'=> ['The maximum should be '.$product->maximum_purchase_qty.' product purchase']])
        ->fails(__('response.fail'));

        if($quantity > @$stock->qty)  return api(['errors'=>['Stock Not Available']])
        ->fails(__('response.fail'));


        $price      = ($stock->price);
        $taxeCollection             =  getTaxesCollection(@$product,$price);
        $taxes                      =  getTaxes(@$product,$price);
        $discount                   =  0;

        if(request()->campaign_uid) {
            $campaign         = Campaign::where('uid',request()->campaign_uid)->first();
            $productCampaign  = CampaignProduct::where('product_id', $product->id)
                                                    ->where('campaign_id', @$campaign->id)
                                                    ->first();
        }

        if (@$productCampaign) {
            $prevPrice = $price;
            $price     = (discount($price, $productCampaign->discount, $productCampaign->discount_type));
            $discount  =  $prevPrice -     $price;
        }
        else {
            if ($product->discount_percentage > 0) {
                $prevPrice = $price;
                $price     = ((cal_discount($product->discount_percentage, $price)));
                $discount  =  $prevPrice -     $price;
            }
        }


        $cart = Cart::where('user_id', $userId)
                        ->where('product_id', $product->id)
                        ->where('attributes_value', $atrribute)
                        ->when($campaign ,function($query) use($campaign){
                            $query->where('campaign_id', $campaign->id);
                        })->first();



        if ($cart) {
            if($campaign){

                    if($campaign->id == $cart->campaign_id && $cart->attributes_value == $atrribute ){
                        $quantity =  $cart->quantity + $quantity ;
                        if( $quantity > $product->maximum_purchase_qty || $quantity >$stock->qty  ){
                            return api(['errors'=>['Already Added!! & Maximum product purchase Quantity exceed']])->fails(__('response.fail'));
                        }
                        $cart->quantity = $quantity;
                        $cart->total    =  ($cart->price*$quantity) ;
                        $cart->save();
                    }
                    else{

                        $taxeCollection             =  getTaxesCollection(@$product,$price);
                        $taxes                      =  getTaxes(@$product,$price);
                        $price                      =  $price + $taxes;
                        $priceWithTax               =  $stock->price + $taxes ;

                        Cart::create([
                            'campaign_id'      => $campaign ? $campaign->id : $cart->campaign_id,
                            'user_id'          => $userId,
                            'session_id'       => null,
                            'discount'         => $discount,
                            'product_id'       => $product->id,
                            'price'            => $price ,
                            'original_price'   => $priceWithTax,
                            'total'            => ($price*$quantity) ,
                            'quantity'         => $quantity ,
                            'taxes'            => $taxeCollection,
                            "total_taxes"      => $taxes,
                            'attribute'        => $request->attribute_id ?? null,
                            'attributes_value' => $atrribute
                        ]);
                    }
            }else{

                $quantity =  $cart->quantity + $quantity ;
                if( $quantity > $product->maximum_purchase_qty || $quantity >$stock->qty  ){
                    return api(['errors'=>['Already Added!! & Maximum product purchase Quantity exceed']])->fails(__('response.fail'));
                }
                $cart->quantity = $quantity;
                $cart->total    =  (round($cart->price*$quantity));
                $cart->save();
            }

        } else {

            $taxeCollection             =  getTaxesCollection(@$product,$price);
            $taxes                      =  getTaxes(@$product,$price);
            $price                      =  $price + $taxes;
            $priceWithTax               =  $stock->price + $taxes ;
            Cart::create([

                'campaign_id'    => @$campaign ? @$campaign->id :null,
                'user_id'        => $userId,
                'session_id'     => null,
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

        return api(['message'=> translate("Product Added To Cart")])
                             ->success(__('response.success'));



    }



    /**
     * Updarte Cart Quantity
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCart(Request $request): JsonResponse
    {


        $validator = Validator::make($request->all(),[
            'uid'      => 'required',
            'quantity' => 'required|integer|min:1',
        ]);


        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $cartItem = Cart::where('uid',$request->uid)->first();

        if(!$cartItem)   return api(['errors'=> [translate("Cart item Not Foundd")]])->fails(__('response.fail'));


        if($request->quantity > $cartItem->product->maximum_purchase_qty)     return api(['errors'=> ['The maximum should be '.$cartItem->product->maximum_purchase_qty.' product purchase']])->fails(__('response.fail'));

        $cartItem->quantity = $request->quantity;
        $cartItem->total    = $cartItem->price*$request->quantity;
        $cartItem->save();

        return api(['errors'=> [translate("Cart item qty has been updated")]])->success(__('response.fail'));

    }


    /**
     * Delete a cart item
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCart(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(),[
            'uid' => 'required',
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        $cartItem = Cart::where('uid',$request->uid)->first();

        if($cartItem ){

            $cartItem->delete();
            return api(['message' => translate('The product item has been deleted from the cart')])->success(__('response.success'));
        }

        return api(['errors' => [translate('This Cart Items Is Not Available')]])->fails(__('response.fail'));


    }


    /**
     * Add to Wish list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function wishlist(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'product_uid' => 'required',
        ]);
        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        $user     = $this->user;
        $product  = Product::where('uid', $request->product_uid)->first();
        if(!$product) return api(['errors' => [translate('Product does not exist')]])->fails(__('response.fail'));

        $wishlist = WishList::where('customer_id', $user->id)
                                ->where('product_id', $product->id)
                                ->first();

        if($wishlist)  return api(['errors' => [translate('Item already added to wishlist')]])->fails(__('response.fail'));

        WishList::create([
            'customer_id' => $user->id,
            'product_id' => $product->id
        ]);

        return api(['message' => translate('Item has been added to wishlist')])->success(__('response.success'));

    }



    /**
     * Delete Item form wishlist
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteWishlist(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'uid' => 'required',
        ]);

        if ($validator->fails()) {
            return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        }

        $user     = $this->user;

        $wishlist = WishList::where('customer_id', $user->id)
                                            ->where('uid', $request->uid)
                                            ->first();
        if($wishlist){
            $wishlist->delete();
            return api(['message' => translate('Item has been Deleted Form Wishlist')])->success(__('response.success'));
        }

        return api(['errors' => [translate('Item Not Found')]])->fails(__('response.fail'));

    }


    /**
     * Update Profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProdile(Request $request): JsonResponse {

        $user = $this->user;

        $validator = Validator::make($request->all(),[
            'name'       => 'required|max:120',
            'username'   => 'required|unique:users,username,'.$user->id,
            'phone'      => 'required|unique:users,phone,'.$user->id,
            'address'    => 'required|max:250',
            'country_id' => 'required|exists:countries,id',
            'city'       => 'required|max:250',
            'state'      => 'required|max:250',
            'zip'        => 'required|max:250',
            'image'      => 'nullable|image',
        ]);

        if ($validator->fails())   return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


        $user->name       = $request->name;
        $user->username   = $request->username;
        $user->phone      = $request->phone;
        $user->country_id = $request->country_id;
        $address = [
            'address'    => $request->input('address'),
            'city'       => $request->input('city'),
            'state'      => $request->input('state'),
            'zip'        => $request->input('zip'),
            'latitude'   => $request->input('latitude'),
            'longitude'  => $request->input('longitude')
        ];

        if($request->hasFile('image')){
            try{
                $removefile  = $user->image ??null;
                $user->image = store_file($request->image, file_path()['profile']['user']['path'], null, $removefile);
            }catch (\Exception $exp){

            }
        }
        $user->address = $address;
        $user->save();

        return api(['message' => translate('Proile Updated Successfully')])->success(__('response.success'));

    }



    /**
     * Update Password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse {

        $validator = Validator::make($request->all(),[
            'current_password' => 'nullable',
            'password'         => 'required|confirmed',
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $user = $this->user;

        if($user->password){
            if(!Hash::check($request->current_password, $user->password)) {
                return api(['errors' => [translate('The password doesnot match')]])->fails(__('response.fail'));
            }
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return api(['message' => translate('Password Updated')])->success(__('response.success'));


    }


    /**
     * Order Checkout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function orderCheckout(Request $request): JsonResponse {


        $user = $this->user;
        $shippingConfiguration =  json_decode(site_settings('shipping_configuration'));
        $rules = [
            'address_id'      => 'required|exists:user_addresses,id',
            'payment_id'      => 'required',
        ];

        if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC"){
            $rules+=[
                    'shipping_method' => 'required|exists:shipping_deliveries,uid',
            ];
        }


        if($user && site_settings('customer_wallet') == StatusEnum::true->status()){
            $rules+=[
                'wallet_payment' => 'required|in:1,0',
            ];
            unset( $rules['payment_id']);
        }


        if (!$user) {
            unset($rules['address_id']);
            $rules += [
                'items'      => 'required|array',
                'email'         => ['required', 'email'],
                'first_name'    => ['required', 'max:255'],
                'last_name'     => ['required', 'max:255'],
                'phone'         => ['required'],
                'address'       => ['required'],
                'zip'           => ['required', 'max:100'],
                'city_id'       => 'required|numeric|exists:cities,id',
                'state_id'      => 'required|numeric|exists:states,id',
                'country_id'    => 'required|numeric|exists:countries,id',
                'latitude'      => 'required|numeric|between:-90,90',
                'longitude'     => 'required|numeric|between:-180,180',
            ];

            if($request->input('create_account') == StatusEnum::true->status()){
                $rules ['email'] = ['required','email', 'unique:users'];
                $rules ['phone'] = ['required', 'unique:users,phone'];
            }
        }


        $validator = Validator::make($request->all(),  $rules);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $created = false;

        if(!$user){

            $address = [
                'address' => $request->input('address'),
                'latitude'=> $request->input('latitude'),
                'longitude'=>$request->input('longitude')
            ];

            if($request->input('create_account') == StatusEnum::true->status()){
                $created = true;
                $user = User::create([
                    'name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'status' => StatusEnum::true->status(),
                ]);


                $billingAddress = UserAddress::create([
                    'name'             => $request->input('name','default'),
                    'user_id'          => $user->id,
                    'first_name'       => $request->input('first_name'),
                    'last_name'        => $request->input('last_name'),
                    'phone'            => $request->input('phone'),
                    'zip'              => $request->input('zip'),
                    'country_id'       => $request->input('country_id'),
                    'city_id'          => $request->input('city_id'),
                    'state_id'         => $request->input('state_id'),
                    'address'          => $address
                ]);

                $accessToken = $user->createToken('authToken')->plainTextToken;
            }else{

                $billingAddress = UserAddress::create([

                    'name'             => $request->input('name','home'),
                    'first_name'       => $request->input('first_name'),
                    'last_name'        => $request->input('last_name'),
                    'phone'            => $request->input('phone'),
                    'zip'              => $request->input('zip'),
                    'country_id'       => $request->input('country_id'),
                    'city_id'          => $request->input('city_id'),
                    'state_id'         => $request->input('state_id'),
                    'address'          => $address
                ]);
            }
        }



        $items  =  ($user && !$created)
                            ? Cart::with('product')->where('user_id',$user->id)->get()
                            : $this->perseList($request->input('items'));

         


        if(site_settings('minimum_order_amount_check') == StatusEnum::true->status()){

            if(!$this->checkoutService->miniMumOrderAmountCheck( $items , Cache::get(CacheKey::API_CURRENCY->value) ,true )){

                return api(
                    [
                        'errors' => [translate('Minimun order amount should be ').show_amount((double)site_settings('minimum_order_amount'),default_currency()->symbol)],
                    ]
                    )->fails(__('response.fail'));

            }

        }


        if($items->count() != 0){


            if(!$request->wallet_payment ||  $request->wallet_payment        ==  StatusEnum::false->status() || 
            site_settings('customer_wallet') == StatusEnum::false->status()){
                    $cashOndelevary = $request->input('payment_id') == StatusEnum::false->status()
                                                        ? true
                                                        : false;

                    if(!@$cashOndelevary){
                            $paymentMethod = PaymentMethod::where('id', $request->input('payment_id'))
                                                            ->active()
                                                            ->first();
                            if(!$paymentMethod) return api(
                                [
                                    'errors' => [translate('Invalid payment gateway')],
                                ]
                                )->fails(__('response.fail'));
                    }

            }





            if($request->address_id) $billingAddress = UserAddress::where("user_id",$this->user->id)->where("id",$request->address_id)->first();

            if(!$billingAddress ) return api(['errors' => [translate('Invalid billing address')],
                ]
                )->fails(__('response.fail'));


            $calculations = $this->checkoutService->calculate($items);

      

            if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC"){
                $shippingResponse['shipping_delivery'] = ShippingDelivery::where('uid',$request->shipping_method)->first();
                $shippingDelivery = $shippingResponse['shipping_delivery'];
                if(!$shippingDelivery) return api(
                    [
                        'errors' => [translate('Invalid shipping method selected')],
                    ]
                    )->fails(__('response.fail'));
    
                $zone = CountryZone::where('country_id',$billingAddress->country_id)->first();

                if(!$zone) return api(
                    [
                        'errors' => [translate('No shipping carrier available in this delivery zone')],
                    ]
                    )->fails(__('response.fail'));
                $calculations['shippingCharge'] = calculateShippingCharge($shippingDelivery,$items,$zone);

            }

            if(@$shippingConfiguration->shipping_option == "LOCATION_BASED"){
                $city                          = City::visible()->findOrfail($billingAddress->city_id);
                $calculations['shippingCharge'] = $city->shipping_fee;
            }

            if($request->input('coupon_code')){

                $coupon = Coupon::where('code', $request->input('coupon_code'))
                                                ->valid()
                                                ->first();
                if(!$coupon) return api(['errors' => [translate('Invalid Coupon Code')]])->fails(__('response.fail'));


                $discount     = round(($coupon->discount($calculations['total_cart_amount'])));

                if(  (int) $discount == 0 ){
                    return api(['errors' => [translate('Sorry, your order total doesnt meet the requirements for this coupon')]])->fails(__('response.fail'));
                }
                $calculations['coupon_amount']     =   $discount;

            }

        
            $order = $this->checkoutService->createOrder($request,$calculations,@$shippingResponse,$this->user ,$billingAddress->id);
            

            if($user){
                $order->customer_id = @$user->id;
                $order->save();
            }


            $order->load(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user']);

            $this->checkoutService->createOrderDetails( $items,$order);
            $this->checkoutService->notifyUser($order ,Cache::get(CacheKey::API_CURRENCY->value));


            

         #HANDLE WALLET PAYMENT
         if($user && $request->wallet_payment ==  StatusEnum::true->status() && 
         site_settings('customer_wallet') == StatusEnum::true->status()){


            if( $user->balance   <   $order->amount) return api(['errors' => [translate('Insufficient Wallet balance !!')]])->fails(__('response.fail'));  
            
            $order->payment_status = Order::PAID;
            $order->wallet_payment = Order::WALLET_PAYMENT;
            $order->status = site_settings('order_status_after_payment',Order::PLACED);
            $order->save();

            OrderDetails::where('order_id',$order->id)->update([
                'status' => site_settings('order_status_after_payment',Order::PLACED)
            ]);

            $transaction = Transaction::create([
                'user_id'            => $user  ? $user->id : null,
                'amount'             => $order->amount,
                'post_balance'       => $user->balance,
                'transaction_type'   => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details'            => 'Order Payement for order id: '.$order->order_id .' Via Wallet',
            ]);

            $user->balance -= $order->amount;
            $user->save();
            $this->checkoutService->cleanCart($items);

            if(@$user && site_settings('club_point_system') == StatusEnum::true->status()){
                WalletRecharge::generatePointLog($user ,$order);
            }


            return api(
                [
                    'message'      => translate('Your order has been submitted'),
                    'order'        => new OrderResource($order),
                    'access_token' => @$accessToken ?? null
                ])->success(__('response.success'));

         }

            

            if(@$cashOndelevary){

                if($user && !$created) $this->checkoutService->cleanCart($items);
                return api(
                    [
                        'message'      => translate('Your order has been submitted'),
                        'order'        => new OrderResource($order),
                        'access_token' => @$accessToken ?? null
                    ])->success(__('response.success'));

            }


            $paymentLog = PaymentInsert::paymentCreate($paymentMethod,$this->user,$order->order_id);


            if($paymentMethod->type == PaymentMethod::MANUAL){

                    $order->payment_details = $request->input("custom_input");
                    $order->payment_method_id = $paymentMethod->id;
                    $order->save();
                    $paymentLog->status = PaymentLog::SUCCESS;
                    $paymentLog->save();
                    if($user && !$created) $this->checkoutService->cleanCart($items);
                    return api(
                        [
                            'message'      => translate('Your order has been submitted'),
                            'order'        => new OrderResource($order),
                            'access_token' => @$accessToken ?? null
                        ])->success(__('response.success'));
            }


            $response = [
                            'message'      => translate('Order Created'),
                            'order'        => new OrderResource($order),
                            'payment_log'  => new PaymentLogResource($paymentLog),
                            'access_token' => @$accessToken ?? false,
                        ];
                    
            $paymentUrl = $this->getPaymentURL($paymentMethod ,$paymentLog);
            if($paymentUrl) $response['payment_url'] = $paymentUrl;

            return api($response)->success(__('response.success'));

        }

        return api(
            [
                'errors' => [translate('Cart Items Not Found')],
            ]
            )->fails(__('response.fail'));

    }




    /**
     * Parse cart items
     *
     * @param mixed $items
     * @return mixed
     */
    public function perseList(mixed $items) : mixed{

        $formattedItems = [];

        foreach($items as $item){

            $uid            = is_array($item) ? $item['uid'] : $item->uid;
            $price          = is_array($item) ? $item['price'] : $item->price;
            $attribute      = is_array($item) ? $item['attribute'] : $item->attribute;
            $qty            = is_array($item) ? $item['qty'] : $item->qty;
            $discount       = is_array($item) ? $item['discount'] : $item->discount;
            $original_price = is_array($item) ? $item['original_price'] : $item->original_price;
            $total_taxes    = is_array($item) ? $item['total_taxes'] : $item->total_taxes;

            $product = Product::where('uid',$uid)->first();
            if($product){
                $cartArr = (object) ([
                    'product_id'       => $product->id,
                    'product'          => $product,
                    'discount'         => $discount,
                    'original_price'   => $original_price,
                    'total_taxes'      => $total_taxes,
                    'price'            => $price,
                    'quantity'         => $qty,
                    'total'            => round($price*$qty),
                    'attributes_value' => $attribute
                ]);
                array_push($formattedItems ,$cartArr);
            }
        }

        return (collect($formattedItems));
    }



    /**
     * Pay now
     *
     * @param int|string $orderUid
     * @param int|string $gateway_code
     * @return JsonResponse
     */
    public function payNow(int|string $orderUid , int|string|null $gateway_code  = null) :JsonResponse{

        $user           = $this->user;

        $order          = Order::with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user'])
                                        ->where("customer_id",   $user->id)
                                        ->where('uid',$orderUid)
                                        ->where('payment_status',Order::UNPAID)
                                        ->first();


        if(!$order) return api(['errors' => ['Order not found']])->fails(__('response.fail'));
        

        if(!$gateway_code && site_settings('customer_wallet') == StatusEnum::false->status() ){
            return api(['errors' => [translate('Wallet payment module is currently inactive')]])->fails(__('response.fail'));
        }

        elseif($gateway_code){

               $paymentLog     = PaymentLog::where('order_id', $order->id)
                                            ->where('status',PaymentLog::PENDING)
                                            ->delete();
                            
                $paymentMethod = PaymentMethod::where('id', $gateway_code)
                                            ->active()
                                            ->first();

                if(!$paymentMethod) return api(['errors' => [translate('Invalid payment gateway')]])->fails(__('response.fail'));


                $paymentLog     = PaymentInsert::paymentCreate($paymentMethod,$this->user ,$order->order_id);



                $response = [
                    'message'      => translate('Order Created'),
                    'order'        => new OrderResource($order),
                    'payment_log'  => new PaymentLogResource($paymentLog),
                ];
        
                $paymentUrl = $this->getPaymentURL($paymentMethod ,$paymentLog);
                if($paymentUrl) $response['payment_url'] = $paymentUrl;
        
                return api(
                    $response
                    )->success(__('response.success'));

        }

        #HANDLE WALLET PAYMENT
        elseif($user  && site_settings('customer_wallet') == StatusEnum::true->status()){

            if( $user->balance   <   $order->amount)  return api(['errors' => [translate('Insufficient Wallet balance !!')]]
            )->fails(__('response.fail'));
            
            $order->payment_status = Order::PAID;
            $order->wallet_payment = Order::WALLET_PAYMENT;
            $order->status = site_settings('order_status_after_payment',Order::PLACED);
            $order->save();
            OrderDetails::where('order_id',$order->id)->update([
                'status' => site_settings('order_status_after_payment',Order::PLACED)
            ]);

            $transaction = Transaction::create([
                'user_id'            => $user  ? $user->id : null,
                'amount'             => $order->amount,
                'post_balance'       => $user->balance,
                'transaction_type'   => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details'            => 'Order Payement for order id: '.$order->order_id .' Via Wallet',
            ]);

            $user->balance -= $order->amount;
            $user->save();

            if($order->order_type ==  Order::DIGITAL) {
                $order->status  = Order::DELIVERED;
                $order->save();
                $this->sendDigitalOrderCommission($order);
            }

            $response = [
                'message'      => translate('Order Payment success'),
                'order'        => new OrderResource($order),
            ];

            if(@$user && site_settings('club_point_system') == StatusEnum::true->status()){
                WalletRecharge::generatePointLog($user ,$order);
            }

            return api(
                $response
                )->success(__('response.success'));

        }


        return api(['errors' => ['Order not found']])->fails(__('response.fail'));

    }


    /**
     * Digital order checkout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function DigitalOrderCheckout(Request $request): JsonResponse
    {
        $user =  $this->user;

        $rules = [
            'digital_attribute_uid' => 'required',
            'product_uid'           => 'required',
            'payment_id'            => 'required',
        ];


        if($user && site_settings('customer_wallet') == StatusEnum::true->status()){
            $rules+=[
                'wallet_payment' => 'required|in:1,0',
            ];
            unset( $rules['payment_id']);
        }

        if(!$request->wallet_payment || $request->wallet_payment        ==  StatusEnum::false->status() || 
        site_settings('customer_wallet')   == StatusEnum::false->status()){

            
             $paymentMethod = PaymentMethod::where('id', $request->input('payment_id'))
                                                        ->active()
                                                        ->first();


              if(!$paymentMethod) return api(['errors' => [translate('Invalid payment gateway')]])->fails(__('response.fail'));

        }


        if(!$user) $rules['email'] = 'required|email';

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails())       return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $product = Product::where('uid', $request->product_uid)->first();

        $digitalProductAttribute = DigitalProductAttribute::where('product_id', $product->id)
                                                           ->where('uid', $request->digital_attribute_uid)->available()
                                                           ->first();

        if(site_settings('minimum_order_amount_check') == StatusEnum::true->status()){

            $cartAmount = default_currency_converter(api_short_amount($digitalProductAttribute->price),Cache::get(CacheKey::API_CURRENCY->value));

            if($cartAmount < (double) site_settings('minimum_order_amount',0)){
                return api(
                    [
                        'errors' => [translate('Minimun order amount should be ').show_amount((double)site_settings('minimum_order_amount'),default_currency()->symbol)],
                    ]
                    )->fails(__('response.fail'));
            }

        }


        $price      =  ($digitalProductAttribute->price);
        $taxes      =  getTaxes(@$product ,$price);
        $price      =  $price  + $taxes;


        if($product->custom_fileds){
            $customInfo =  [];
            foreach($product->custom_fileds as $key => $value){
              $customInfo [$value->data_name] =  $request->input($value->data_name);

            }
       }



        $order = Order::create([
            'customer_id'         => $user?->id,
            'order_id'            => site_settings('order_prefix').random_number(),
            'amount'              => $price ,
            'original_amount'     => $digitalProductAttribute->price,

            'total_taxes'         =>       $taxes ,
            'payment_type'        => Order::PAYMENT_METHOD,
            'payment_status'      => Order::UNPAID,
            'status'              => Order::PLACED,
            'order_type'          => Order::DIGITAL,
            'custom_information' => @$customInfo ?? null,
            'billing_information' => $request->email ? ['email' => $request->email,'username' => $request->email]: ['email' => $user->email,'username' => $user->name],

        ]);

        OrderDetails::create([
            'order_id'                     => $order->id,
            'product_id'                   => $product->id,
            'digital_product_attribute_id' => $digitalProductAttribute->id,
            'quantity'                     => 1,
            'total_price'                  => $order->amount,
            'original_price'               => $order->original_amount,
            'total_taxes'                  => $order->total_taxes,

        ]);

        $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
        $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
        $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
        $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;


        $mailCode = [
            'order_number'     => $order->order_id,
            'time'             => Carbon::now(),
            'payment_status'   => $order->payment_status == Order::PAID ? 'Paid' :"Unpaid",
            'amount'           => show_amount($order->amount,Cache::get(CacheKey::API_CURRENCY->value)),
            'customer_phone'           => @$phone ?? 'N/A',
            'customer_email'           => @$email,
            'customer_name'            => @$first_name ?? "N/A",
            'customer_address'         => @$address ?? 'N/A',
        ];

        SendMailJob::dispatch($user ?? $order->billing_information ,'DIGITAL_PRODUCT_ORDER',$mailCode);


         #HANDLE WALLET PAYMENT
         if($user && $request->wallet_payment ==  StatusEnum::true->status() && 
         site_settings('customer_wallet') == StatusEnum::true->status()){

            if( $user->balance   <   $order->amount)  return api(['errors' => [translate('Insufficient Wallet balance !!')]])->fails(__('response.fail'));
            
            $order->status         = Order::DELIVERED;
            $order->payment_status = Order::PAID;
            $order->wallet_payment = Order::WALLET_PAYMENT;
            $order->save();

            $transaction = Transaction::create([
                'user_id'            => $user  ? $user->id : null,
                'amount'             => $order->amount,
                'post_balance'       => $user->balance,
                'transaction_type'   => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details'            => 'Order Payement for order id: '.$order->order_id .' Via Wallet',
            ]);

            $user->balance -= $order->amount;
            $user->save();

            $this->sendDigitalOrderCommission($order);

            
            if(@$user && site_settings('club_point_system') == StatusEnum::true->status()){
                WalletRecharge::generatePointLog($user ,$order);
            }

            return api(
                [
                    'message'      => translate('Order Created'),
                    'order'        => new OrderResource($order),
                ]
                )->success(__('response.success'));

         }


        $paymentLog = PaymentInsert::paymentCreate( $paymentMethod  ,$this->user ,$order->order_id ,);


        $response = [
            'message'      => translate('Order Created'),
            'order'        => new OrderResource($order),
            'payment_log'  => new PaymentLogResource($paymentLog),
        ];

        $paymentUrl = $this->getPaymentURL($paymentMethod ,$paymentLog);
        if($paymentUrl) $response['payment_url'] = $paymentUrl;

        return api( $response)->success(__('response.success'));


    }



    /**
     * Ordeer checkout success
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function  checkoutSuccess(Request $request) :JsonResponse{

        $validator = Validator::make($request->all(),[
            'trx_id'       => 'required',
            'status'       => 'required',
            'payment_data' => 'required_if:status,success',
        ]);

        if ($validator->fails()){
            return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        }

        $paymentLog = PaymentLog::where('trx_number', $request->trx_id)->first();

        if( $paymentLog ){
            if($request->status =='success'){
                Order::where('order_id',$request->trx_id)->update([
                    'payment_info'=>   $request->payment_data
                ]);
                PaymentInsert::paymentUpdate($request->trx_id);
                return api(
                    [
                        'message' => translate('Transaction Completed'),
                    ]
                    )->success(__('response.success'));
            }
            elseif($request->status =='cancel' ||  $request->status =='failed'){

                if($paymentLog->status == 1) {
                    $paymentLog->status = 3;
                    $paymentLog->save();
                }

                return api(
                    [
                        'errors' => [translate('Order Cancel or Failed')],
                    ]
                    )->fails(__('response.fail'));
            }
        }

        return api(
            [
                'errors' => [ translate('Invalid Transaction Id') ],
            ]
            )->fails(__('response.fail'));



    }



    /**
     * Track Order
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trackOrder(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required',
        ]);


        $order = Order::with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user'])
                                ->where('order_id', $request->order_id)
                                ->first();

        if(!$order){
            return api(
                [
                    'errors' => [translate('Invalid OrderId')],
                ]
            )->fails(__('response.fail'));
        }

        return api(
        [
            'message' => translate('Order founded'),
            'order' => new OrderResource($order),
        ]
        )->success(__('response.success'));

    }




    /**
     * Get all support tickets
     *
     * @return JsonResponse
     */
     public function supportTicket() :JsonResponse{

        $user     = auth()->user();
        $tickets  = SupportTicket::with(['messages'])->where('user_id', $user->id)
                     ->latest()
                     ->paginate(site_settings('pagination_number',10));
        return api(
            [
                'tickets' => new TicketCollection($tickets),
            ]
            )->success(__('response.success'));
     }




     /**
      * Get ticket details
      *
      * @param int | string $ticketId
      * @return JsonResponse
      */
     public function ticketDetails(string | int $ticketId) :JsonResponse{


        $user     = auth()->user();

        $ticket   = SupportTicket::with(['messages','messages.supportfiles'])
                                    ->where('user_id', $user->id)
                                    ->where('ticket_number',$ticketId)
                                    ->first();
        return api(
            [
                'ticket'          => new TicketResource($ticket),
                'ticket_messages' => TicketMessageResource::collection($ticket->messages),
            ])->success(__('response.success'));

     }



     /**
      * Download ticket file
      *
      * @param int | string $id
      *
      * @return JsonResponse
      */
     public function supportTicketDownlod(string | int $id) :JsonResponse{

        $supportFile = SupportFile::find(($id));

        if(!$supportFile){
            return api(['errors' => ['File not found']])->fails(__('response.fail'));
        }

        $file        = @$supportFile->file;


        return api(
            [
                'url'   => @show_image(file_path()['ticket']['path'].'/'.$file)
            ])->success(__('response.success'));

     }


     /**
      * Close a ticket
      *
      * @param int | string $ticketId
      *
      * @return JsonResponse
      */
     public function closedTicket(string | int $ticketId) :JsonResponse{

        $supportTicket =  SupportTicket::where('ticket_number',$ticketId)->first();

        if(!$supportTicket) return api(['errors' => ['Ticket not found']])->fails(__('response.fail'));

        $supportTicket->status  = SupportTicket::CLOSED;
        $supportTicket->save();

        return api(
            [
                'message'   => 'Ticket Cloesd'
            ])->success(__('response.success'));

     }



     /**
      * Create a new Ticket
      *
      * @param Request $request
      * @return JsonResponse
      */
     public function ticketStore(Request $request) :JsonResponse{

        $this->validate($request, [

            'name'    => 'required|max:255',
            'email'   => 'required|email',
            'subject' => 'required|max:255',
            'message' => 'required',
            'file.*'  => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')]
        ]);

        $supportTicket                = new SupportTicket();
        $supportTicket->ticket_number = random_number();
        $supportTicket->user_id       = auth()->user()->id ?? null;
        $supportTicket->name          = $request->name;
        $supportTicket->email         = $request->email;
        $supportTicket->subject       = $request->subject;
        $supportTicket->priority      = 2;
        $supportTicket->status        = 1;
        $supportTicket->save();

        $message                      = new SupportMessage();
        $message->support_ticket_id   = $supportTicket->id;
        $message->admin_id            = null;
        $message->message             = $request->message;
        $message->save();

        if($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                try {
                    $supportFile                     = new SupportFile();
                    $supportFile->support_message_id = $message->id;
                    $supportFile->file               = upload_new_file($file, file_path()['ticket']['path']);
                    $supportFile->save();
                } catch (\Exception $exp) {

                }
            }
        }

        return api(
            [
                'ticket'          => new TicketResource($supportTicket),
                'ticket_messages' => TicketMessageResource::collection($supportTicket->messages),
            ])->success(__('response.success'));

     }

     /**
      * Reply a new Ticke
      *
      * @param Request $request
      * @return JsonResponse
      */
     public function ticketReply(Request $request ,int | string $ticketNumber) :JsonResponse{

        $request->validate([
            'message'  => 'required',
            'file.*'   => ["nullable", new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')]

        ]);

        $supportTicket           = SupportTicket::where('ticket_number', $ticketNumber)->first();

        if(!$supportTicket) return api(['errors' => ['Ticket not found']])->fails(__('response.fail'));

        $supportTicket->user_id  = auth()->user()->id ?? null;
        $supportTicket->status   = 3;
        $supportTicket->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $supportTicket->id;
        $message->message           = $request->message;
        $message->save();

        if($request->hasFile('file')){

            foreach ($request->file('file') as $file) {
                    try {
                        $supportFile                     = new SupportFile();
                        $supportFile->support_message_id = $message->id;
                        $supportFile->file               = upload_new_file($file, file_path()['ticket']['path']);
                        $supportFile->save();
                    } catch (\Exception $exp) {

                    }
                }
        }


        return api(
            [
                'ticket'           => new TicketResource($supportTicket),
                'ticket_messages'  => TicketMessageResource::collection($supportTicket->messages),
            ])->success(__('response.success'));

     }



     /**
      * Get a Oder details

      * @param int | string $order_id
      *
      */
     public function orderDetails(int | string $order_id) :JsonResponse{

        $order = Order::with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user'])->where('uid', $order_id)->first();

        if(!$order) return api(['errors' => ['Order not found']])->fails(__('response.fail'));

        return api(['order'       => new OrderResource($order)])->success(__('response.success'));

     }




    public function deliveryManRating(Request $request) :JsonResponse{


            $validator = Validator::make($request->all(),[
                'deliveryman_id'       => 'required:exists:delivery_men,id',
                'order_id'             => 'required:exists:orders,id',
                'rating'               => 'required|gt:0, max:5',
                'message'              => 'required|max:191',
            ]);


            if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

            $deliveryman = DeliveryMan::find($request->deliveryman_id);

            if(!$deliveryman) return api(['errors'=>[translate("Deliverman not found")]])->fails(__('response.fail'));


            $order       = Order::where("id",$request->order_id)
                                            ->where("delivery_man_id",$request->deliveryman_id)
                                            ->where("customer_id",$this->user->id)
                                            ->first();


            if(!$order) return api(['errors'=>[translate("Invalid order")]])->fails(__('response.fail'));


            if(DeliveryManRating::where('delivery_men_id',$deliveryman->id)
                            ->where("user_id",$this->user->id)
                            ->where("order_id",$order->id)
                            ->exists()){

                return api(['errors'=>[translate("You already ratted this deliveryman")]])->fails(__('response.fail'));
            }

            DeliveryManRating::create([
                'user_id'         => $this->user->id,
                'order_id'        => $order->id,
                'delivery_men_id' => $deliveryman->id,
                'message'         => $request->message,
                'rating'         => $request->rating
            ]);



            return api(
                [
                    'message'  => translate('Thanks for your feedback'),
                ])->success(__('response.success'));

    }


    /**
     * Update FCM TOKEN
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function  updateFcmToken(Request $request) :JsonResponse{

        $validator = Validator::make($request->all(),[
            'fcm_token'       => 'required',
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $this->user->fcm_token = $request->input('fcm_token');
        $this->user->save();

        return api(
            [
                'message'   => translate("Token updated")
            ])->success(__('response.success'));


    }

 
    public function rewardLog() :JsonResponse {


        if(site_settings('club_point_system' ,0) == StatusEnum::false->status())  return api(['errors'=>  translate('Reward point system is currently inactive') ])->fails(__('response.fail'));

        $rewardOverView = [
             'total'   => RewardPointLog::date()
                                ->where('user_id',$this->user->id)
                                ->sum('point'),
            'pending'  => (int) RewardPointLog::date()
                                        ->where('user_id',$this->user->id)
                                        ->pending()
                                        ->sum('point'),
            'redeemed' => (int) RewardPointLog::date()
                                        ->where('user_id',$this->user->id)
                                        ->redeemed()
                                        ->sum('point'),
            'expired'  => (int) RewardPointLog::date()
                                        ->where('user_id',$this->user->id)
                                        ->expired()
                                        ->sum('point')
        ];


        $rewardLogs  = RewardPointLog::with(['order','product'])->date()->filter()
                                   ->where('user_id',$this->user->id)
                                   ->paginate(site_settings('pagination_number',10));

        return api([
            'reward_overview' => $rewardOverView ,
            'reward_logs'    => new RewardPointCollection($rewardLogs),
        ])->success(__('response.success'));

    }



    public function reedemPoint(Request $request) :JsonResponse {


        if(site_settings('club_point_system' ,0) == StatusEnum::false->status())  return api(['errors'=>  translate('Reward point system is currently inactive') ])->fails(__('response.fail'));


        $validator = Validator::make($request->all(),[
            'reward_id'       => 'required:exists:reward_point_logs,id'
        ]);
        

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


        $rewardLog = RewardPointLog::with(['order','product'])
                                        ->where('user_id',$this->user->id)
                                        ->where('id',$request->input('reward_id'))
                                        ->pending()
                                        ->first();


        if(!$rewardLog)  return api(['errors'=> [translate('Invalid reward log')]])->fails(__('response.fail'));


        $point = $rewardLog->point;
        $conversionRate = (int) site_settings('customer_wallet_point_conversion_rate',1); 
        $amount = round($point / $conversionRate,4);


        #CREATE TRANSACTION
        $transaction = Transaction::create([
            'user_id'            => $this->user->id ,
            'amount'             => $amount,
            'post_balance'       => $this->user->balance,
            'transaction_type'   => Transaction::PLUS,
            'transaction_number' => trx_number(),
            'details'            => 'Balance added by redeeming '.$point  .' points',
        ]);
                
        $this->user->balance += $amount;
        $this->user->save();

        $rewardLog->status = RewardPointStatus::REDEEMED->value;

        $rewardLog->redeemed_at = Carbon::now();
        $rewardLog->save();



        return api(
            [
                'message'   => translate("Redeemed successfully")
            ])->success(__('response.success'));



    }







}
