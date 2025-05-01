<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentMethod\PaymentController;
use App\Http\Services\Frontend\CheckoutService;
use App\Http\Services\Frontend\ProductService;
use App\Http\Utility\Wallet\WalletRecharge;
use Illuminate\Http\Request;
use App\Models\ShippingDelivery;
use App\Models\PaymentMethod;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\SendMail;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryZone;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    protected CheckoutService $checkoutService;
    protected ProductService $productService;
    protected ?User $user;
    public function __construct()
    {
        $this->checkoutService = new CheckoutService();
        $this->productService = new ProductService();

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web')?->load(['billingAddress']);
            return $next($request);
        });
    }




    public function shippingCarrier(Request $request): array
    {

        $rules = [
            'country_id'    => 'required|exists:countries,id',
            'address_id' => 'required|exists:user_addresses,id',
        ];

        $unsetKey = $this->user ? "country_id" : "address_id";

        unset($rules[$unsetKey]);
        $request->validate($rules,[
            'address_id.required' => translate("Please select billing address first"),
            'country_id.required' => translate("Please select billing address first"),
        ]);


        $countryId  = $request->input("country_id");
        if($request->address_id){
            $address =  UserAddress::where('id',$request->address_id)->firstOrfail();
            $countryId  =  $address->country_id;
        }

        $country = Country::visible()->where("id",$countryId)->first();
        $zone       = CountryZone::where('country_id',$country->id)->first();

        if(!$zone){
            return [
                'status'  => false,
                'message' => translate("No shipping carrier available in this delivery zone")
            ];
        }


        $items      = $this->productService->getCartItem($this->user);
        $weight = 0;
        $price  = 0;

        foreach($items as $item){
            if($item->product){
                $weight+= $item->product->weight*$item->quantity;
            }
            $price += $item->total;
        }

        $shippingDeliverys        = ShippingDelivery::active()
                                        ->orderBy('id', 'DESC')
                                        ->get()
                                        ->map(function(ShippingDelivery $shippingDelivery) use ($zone ,  $weight ,  $price){
                                                $filterValue =  @$shippingDelivery->shipping_type == 'weight_wise' ? $weight : $price;
                                                if($shippingDelivery->free_shipping ==  StatusEnum::true->status()){
                                                    $price = 0;
                                                }else{
                                                    $configuration = collect($shippingDelivery->price_configuration)
                                                    ->filter(function ($item) use ($filterValue ,$zone) {
                                                        return $item->zone_id == $zone->zone_id &&
                                                            $filterValue > $item->greater_than &&
                                                            $filterValue <= $item->less_than_eq;
                                                    })->first();

                                                    $price =   @$configuration->cost?? 0;
                                                }

                                           return (object)[
                                                "id"            => $shippingDelivery->id,
                                                "name"          => $shippingDelivery->name,
                                                "duration"      => $shippingDelivery->duration,
                                                "description"   => $shippingDelivery->description,
                                                "shipping_type" => @$shippingDelivery->shipping_type,
                                                "price"         => $price,
                                           ];
                                        });





        return [
            'status'  => true,
            'shipping_carrier' => view('frontend.partials.shipping_configuration', compact('shippingDeliverys'))->render()
        ];

    }




    public function getShippingMethod(Request $request): array
    {


        $rules = [
            'country_id'    => 'required|exists:countries,id',
            'address_id' => 'required|exists:user_addresses,id',
        ];

        $unsetKey = $this->user ? "country_id" : "address_id";

        unset($rules[$unsetKey]);
        $request->validate($rules);



        $items      = $this->productService->getCartItem($this->user);

        $method_id = $request->method_id;
        $items      = $this->productService->getCartItem($this->user);

        $shippingDelivery        = ShippingDelivery::active()->where('id',$method_id)->firstOrfail();
        $countryId  = $request->input("country_id");
        if($request->address_id){
            $address =  UserAddress::where('user_id',$this->user->id)->where('id',$request->address_id)->firstOrfail();
            $countryId  =  $address->country_id;
        }

        $country = Country::visible()->where("id",$countryId)->first();

        $zone = CountryZone::where('country_id',$country->id)->first();

        if(!$zone){
            return [
                'status'  => false,
                'message' => translate("No shipping carrier available in this delivery zone")
            ];
        }

        $shippingDeliveryCharge = calculateShippingCharge($shippingDelivery ,$items,    $zone );


        return [
            'status'        => true,
            'order_summary' => view('frontend.partials.order_summary', compact('items','shippingDeliveryCharge'))->render()
        ];

    }


    public function shippingCity(Request $request): array
    {

        $rules = [
            'city_id'    => 'required|exists:cities,id',
            'address_id' => 'required|exists:user_addresses,id',
        ];

        $unsetKey = $this->user ? "city_id" : "address_id";

        unset($rules[$unsetKey]);

        $request->validate($rules);

        $items      = $this->productService->getCartItem($this->user);

        $cityId = $request->city_id;
        if($request->address_id){
            $address =  UserAddress::where('user_id',$this->user->id)->where('id',$request->address_id)->firstOrfail();
            $cityId  =  $address->city_id;
        }
        $city = City::visible()->where('id', $cityId)->firstOrfail();

        return [
            'status' => true,
            'order_summary' => view('frontend.partials.order_summary', compact('items','city'))->render()
        ];

    }



    /**
     * Get checkout view
     *
     * @return  View | RedirectResponse
     */
    public function checkout(): View | RedirectResponse
    {


        $title      = translate('Checkout');
        $user       = $this->user;
        $items      = $this->productService->getCartItem($user);

        $countries  = Country::visible()->with(['states'=>fn(HasMany $q) => $q->visible(),'states.cities'=>fn(HasMany $q) => $q->visible()])->get();

        if ($items->count() == 0) {
            return back()->with("error", translate('No product added to your cart'));
        }

        # check minimum order amount
        if (site_settings('minimum_order_amount_check') == StatusEnum::true->status()) {
            if (!$this->checkoutService->miniMumOrderAmountCheck($items, session()->get('web_currency'))) {
                return back()->with('error', translate('Minimun order amount should be ') . show_amount((float)site_settings('minimum_order_amount'), default_currency()->symbol));
            }
        }
        $paymentMethods           = active_payment_methods();
        $manualPaymentMethods     = active_manual_payment_methods();

        $shippingDeliverys        = ShippingDelivery::active()
                                        ->orderBy('id', 'DESC')
                                        ->get();

        return view('frontend.checkout', compact('title', 'items', 'shippingDeliverys', 'paymentMethods', 'user', 'manualPaymentMethods' , 'countries'));
    }



    /**
     * Store a physical order
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function order(Request $request): RedirectResponse
    {


        $shippingConfiguration =  json_decode(site_settings('shipping_configuration'));
        $rules = [
            'address_id'      => 'required|exists:user_addresses,id',
            'payment_id'      => 'required',
        ];

        if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC"){
            $rules+=[
                    'shipping_method' => 'required|exists:shipping_deliveries,id',
            ];
        }

        if(auth_user('web') && site_settings('customer_wallet') == StatusEnum::true->status()){
            $rules+=[
                'wallet_payment' => 'required|in:1,0',
            ];
            unset( $rules['payment_id']);
        }

        if (!$this->user) {
            unset($rules['address_id']);
            $rules += [
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

            if ($request->input('create_account') == StatusEnum::true->status()) {
                $rules['email'] = ['required', 'email', 'unique:users'];
                $rules['phone'] = ['required', 'unique:users,phone'];
            }
        }

        $request->validate($rules, ['address_id.required' => translate('Please Add Billing Address First')]);


        $items = $this->productService->getCartItem($this->user);


        # check minimum order amount
        if (site_settings('minimum_order_amount_check') == StatusEnum::true->status()) {
            if (!$this->checkoutService->miniMumOrderAmountCheck($items, session()->get('web_currency')))  return redirect()->route('home')->with('error', translate('Minimun order amount should be ') . show_amount((float)site_settings('minimum_order_amount'), default_currency()->symbol));
        }

        
        if (!$this->user) {

            $address = [
                'address' => $request->input('address'),
                'latitude'=> $request->input('latitude'),
                'longitude'=>$request->input('longitude')
            ];

            if($request->input('create_account') == StatusEnum::true->status()){

                    $user = User::create([
                        'name'      => $request->input('first_name'),
                        'last_name' => $request->input('last_name'),
                        'email'     => $request->input('email'),
                        'phone'     => $request->input('phone'),
                        'status'    => StatusEnum::true->status(),
                    ]);

                    $billingAddress = UserAddress::create([
                        'name'             => $request->input('name','default'),
                        'email'            => $request->input('email'),
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
                    Auth::guard('web')->login($user);
                    $this->user = $user;
                    $this->productService->updateCart($user);
            }else{

                $billingAddress = UserAddress::create([
                    'name'             => $request->input('name','home'),
                    'email'            => $request->input('email'),
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


        if($request->address_id) $billingAddress = UserAddress::where("user_id",$this->user->id)->where("id",$request->address_id)->firstOrfail();

        if(!$billingAddress ) return back()->with("error",translate("Invalid billing address"));


        if(!$request->wallet_payment || $request->wallet_payment        ==  StatusEnum::false->status() || 
          site_settings('customer_wallet') == StatusEnum::false->status()){

     
            $cashOndelevary = $request->input('payment_id') == StatusEnum::false->status()
                                                    ? true
                                                    : false;



            if (!$cashOndelevary) {
                $paymentMethod = PaymentMethod::where('id', $request->input('payment_id'))
                                            ->active()
                                            ->first();
                if (!$paymentMethod)   return back()->with("error", translate("Invalid payment gateway"));
            }
        }
                

        $order = DB::transaction(function () use ($items, $request ,$billingAddress ,$shippingConfiguration) {

            $calculations     = $this->checkoutService->calculate($items);

            if(@$shippingConfiguration->shipping_option == "LOCATION_BASED"){

                $city                          = City::visible()->where('id',$billingAddress->city_id)->first();
                $calculations['shippingCharge'] = $city->shipping_fee;
            }

            if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC")  {
                 $shippingResponse =     $this->checkoutService->shippingData($request, $this->user);
                 $shippingDelivery = $shippingResponse['shipping_delivery'];
                 $zone = CountryZone::where('country_id',$billingAddress->country_id)->firstOrfail();
                 $calculations['shippingCharge'] = calculateShippingCharge($shippingDelivery ,$items,$zone );
            }

            $order            = $this->checkoutService->createOrder($request, $calculations, @$shippingResponse, $this->user ,$billingAddress->id);

            $this->checkoutService->createOrderDetails($items, $order);
            return  $order;
        });

        #NOTIFY USER
        $this->checkoutService->notifyUser($order);

         
         #HANDLE WALLET PAYMENT
         if(auth_user('web') && $request->wallet_payment ==  StatusEnum::true->status() && 
         site_settings('customer_wallet') == StatusEnum::true->status()){

            $user = auth_user('web');

            if( $user->balance   <   $order->amount)  return redirect()->back()->with('error', translate("Insufficient Wallet balance !!"));

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

            if(site_settings('club_point_system') == StatusEnum::true->status()){
                WalletRecharge::generatePointLog($user ,$order);
            }


            $this->checkoutService->cleanCart($items);
           return redirect()->route('order.success', $order->order_id)->with('success', translate("Your order has been submitted"));

         }



        if (@$cashOndelevary) {
            $this->checkoutService->cleanCart($items);
            return redirect()->route('order.success', $order->order_id)->with('success', translate("Your order has been submitted"));
        }


        if ($paymentMethod && $paymentMethod->type == PaymentMethod::MANUAL) {
            $order->payment_method_id = $paymentMethod->id;
            $order->save();
        }

        session()->put('order_id', $order->order_id);

        PaymentInsert::paymentCreate($paymentMethod, $this->user);

        return redirect()->route('user.payment.confirm');
    }


    public function orderRecheckout(Request $request){



        $rules = [
            'id'              => 'required|exists:orders,id',
            'payment_id'      => 'required',
        ];


        if(site_settings('customer_wallet') == StatusEnum::true->status()){
            $rules+=[
                'wallet_payment' => 'required|in:1,0',
            ];
            unset( $rules['payment_id']);
        }

        $request->validate($rules);

        $user           = $this->user;

        $order          = Order::with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user'])
                                        ->where("customer_id",   $user->id)
                                        ->where('id',$request->input('id'))
                                        ->where('payment_status',Order::UNPAID)
                                        ->firstOrfail();

        $paymentId = $request->input('payment_id');

    
        
        if(!$paymentId && 
            $request->input('wallet_payment') ==  1 && 
            site_settings('customer_wallet') == StatusEnum::false->status() ){
            return back()->with('error',translate('Wallet payment module is currently inactive'));
        }

        if($paymentId){

                $paymentLog     = PaymentLog::where('order_id', $order->id)
                                            ->where('status',PaymentLog::PENDING)
                                            ->delete();
                            
                $paymentMethod = PaymentMethod::where('id', $paymentId)
                                            ->active()
                                            ->firstOrfail();

                if(!$paymentMethod)  return back()->with('error',translate('Invalid payment method'));


                $paymentLog     = PaymentInsert::paymentCreate($paymentMethod,$this->user ,$order->order_id);


               session()->put('payment_track', $paymentLog->trx_number);
               return (new PaymentController())->paymentConfirm();


        }
        elseif(!$paymentId && site_settings('customer_wallet') == StatusEnum::true->status()){

            if( $user->balance   <   $order->amount) return back()->with('error',translate('Insufficient Wallet balance !!'));
            
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

            if(@$user && site_settings('club_point_system') == StatusEnum::true->status())   WalletRecharge::generatePointLog($user ,$order);

            return redirect()->route('user.dashboard')->with('success',translate('Order Payment success'));


        }




    }
}
