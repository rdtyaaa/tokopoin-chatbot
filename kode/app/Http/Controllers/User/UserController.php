<?php

namespace App\Http\Controllers\User;

use App\Enums\RewardPointStatus;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentMethod\PaymentController;
use App\Http\Services\Frontend\UserService;
use App\Models\DeliveryManOrder;
use App\Models\RewardPointLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WishList;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use App\Models\ShippingDelivery;
use App\Models\Follower;
use App\Models\ProductRating;
use App\Models\DigitalProductAttribute;
use App\Models\OrderDetails;
use App\Models\PaymentMethod;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Hash;
use App\Models\Seller;
use App\Models\Attribute;
use App\Models\Refund;
use App\Models\Subscriber;
use App\Models\User;
use App\Http\Services\Frontend\ProductService;
use App\Models\Country;
use App\Models\DeliveryMan;
use App\Models\DeliveryManRating;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{


    protected UserService $userService;
    protected ProductService $productService;
    protected ?User $user;
    public function __construct()
    {
        $this->userService = new UserService();
        $this->productService = new ProductService();

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web')?->load('billingAddress');
            return $next($request);
        });
    }



    public function index(Request $request)
    {

        $title = translate('User Dashboard');

        $user = User::with(['follower' => function ($q) {
            return $q->with(['seller' => function ($q) {
                return $q->with(['sellerShop']);
            }]);
        }, 'reviews', 'reviews.product', 'reviews.product.brand' ])->where('id', Auth::user()->id)->first();


        $items = $this->productService->getCartItem($this->user);

        $orders = Order::with(['refund', 'log'])->where('customer_id', $user->id)->orderBy('id', 'DESC')->physicalOrder()->latest();
        session()->put('order_search', $request->search_data);
        if ($request->search_data && $request->search_data != 'all') {
            $date = \Carbon\Carbon::today()->subDays($request->search_data);
            $orders = $orders->where('created_at', '>=', $date)->paginate(site_settings('pagination_number', 10))->appends(request()->all());
        } else {
            $orders = $orders->paginate(site_settings('pagination_number', 10))->appends(request()->all());
        }
        $wishlists = $this->userService->wishlistItems();

        $digitalOrders = Order::where('customer_id', $user->id)->orderBy('id', 'DESC')->digitalOrder()->latest()->paginate(site_settings('pagination_number', 10));
        return view('user.dashboard', compact('title', 'user', 'orders', 'items', 'digitalOrders', 'wishlists'));
    }

    public function supportTicket()
    {
        $user = Auth::user();
        $title = translate('Support Ticket');
        $supportTickets = SupportTicket::with(['messages'])->where('user_id', $user->id)->latest()->paginate(site_settings('pagination_number', 10));
        return view('user.ticket', compact('title', 'supportTickets', 'user'));
    }



    public function transactions()
    {
        $user = Auth::user();
        $title = translate('Transactions');
        $transactions = Transaction::whereNotNull('user_id')
                                    ->search()
                                    ->date()
                                    ->where('user_id', $user->id)
                                    ->latest()
                                    ->paginate(site_settings('pagination_number',10))
                                    ->appends(request()->all());
        return view('user.transactions', compact('title', 'transactions', 'user'));
    }



    public function addressStore(Request $request)
    {
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

        $request->validate($rules);

        $user    = auth_user('web');

        $address = [
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ];

        UserAddress::create([
            'name'             => $request->input('address_name','default') ,
            'email'            => $request->input('email') ,
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



        return back()->with('success', translate('Address Created'));
    }

    public function addressEdit(Request $request)
    {

        $rules = [
            'id'          => ['required','exists:user_addresses,id'],
        ];

        $request->validate($rules);

        $user        = auth_user('web');
        $userAddress = UserAddress::where('id',$request->id)->where('user_id',$user->id)->firstOrfail();
        $countries   = Country::visible()->with(['states'=>fn(HasMany $q) => $q->visible(),'states.cities'=>fn(HasMany $q) => $q->visible()])->get();

        return json_encode([
            "html" => view("frontend.edit_address_checkout" , compact('userAddress', 'countries'))->render()
        ]);

    }



    public function addressUpdate(Request $request)
    {

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



        $request->validate($rules);

        $user    = auth_user('web');
        $userAddress = UserAddress::where('id',$request->id)->where('user_id',$user->id)->firstOrfail();

        $address = [
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ];

        $userAddress->name       = $request->input('address_name','default');
        $userAddress->email      = $request->input('email');
        $userAddress->first_name = $request->input('first_name');
        $userAddress->last_name  = $request->input('last_name');
        $userAddress->zip        = $request->input('zip');
        $userAddress->country_id = $request->input('country_id');
        $userAddress->city_id    = $request->input('city_id');
        $userAddress->state_id   = $request->input('state_id');
        $userAddress->address    =  $address;
        $userAddress->save();

        return back()->with('success', translate('Address Updated'));
    }


    public function addressDelete($id)
    {

        $user        = auth_user('web');
        $userAddress = UserAddress::withCount(['orders'])->where('id',$id)
                                   ->where('user_id',$user->id)
                                   ->firstOrfail();

        if($userAddress->orders_count == 0){
            $userAddress->delete();
            return back()->with('success', translate('Address Deleted'));
        }

        return back()->with('error', translate('Address cannot be deleted!!address has order under it'));

    }




    public function orderDetails($orderNumber)
    {
        $title = translate('Order Details');
        $user = Auth::user();
        $order = Order::where('customer_id', $user->id)->where('order_id', $orderNumber)->physicalOrder()->first();
        $orderDetails = OrderDetails::where('order_id', $order->id)->with('product', 'product.brand', 'product.review')->get();
        return view('user.order_details', compact('title', 'orderDetails', 'order'));
    }

    public function digitalOrderDetails($orderNumber)
    {
        $title = translate('Digital order details');
        $user = Auth::user();
        $order = Order::where('customer_id', $user->id)->where('order_id', $orderNumber)->digitalOrder()->first();
        if (!$order)    return back();
        $orderDetail = OrderDetails::where('order_id', $order->id)->first();
        $digitalProductAttributes = DigitalProductAttribute::where('id', $orderDetail->digital_product_attribute_id)->first();
        return view('user.digital_order_details', compact('title','order', 'orderDetail', 'digitalProductAttributes'));
    }

    public function wishlistItem()
    {

        $title = translate('Wishlist items');
        $wishlists = $this->userService->wishlistItems();
        return view('user.wish_list', compact('title', 'wishlists'));
    }

    public function reviews()
    {
        $title   = translate('All product reviews');
        $user    = Auth::user();

        return view('user.reviews', compact('title', 'user'));
    }

    /**
     * get product attribute
     */
    public function getProductAttribute()
    {
        $productId = request()->get('product_id');

        $product =  Product::with('stock')->where('id', $productId)->first();

        $attributeIds = array_unique($product->stock->pluck('attribute_id')->toArray());

        $attributeInfos = Attribute::with(['value' => function ($q) use ($product) {
            return $q->whereIn('name', $product->stock->pluck('attribute_value')->toArray());
        }])->whereIn('id', $attributeIds)->get();

        $array = [];
        foreach ($attributeInfos as $attributeInfo) {
            $array[$attributeInfo->name . '+' . $attributeInfo->id] = $attributeInfo->value->pluck('name')->toArray();
        }
        return json_encode([
            'data' => $array,
            'productId' => $productId
        ]);
    }

    public function shoppingCart()
    {
        $title = translate('All shopping cart');
        $user = Auth::user();
        $items = Cart::with(['product', 'product.brand'])->where('user_id', $user->id)->orWhere('session_id', session()->get('session_id'))->orderBy('id', 'desc')->paginate(site_settings('pagination_number', 10));
        return view('frontend.view_cart', compact('title', 'items'));
    }

    public function digitalOrder()
    {
        $title = translate('All digital order list');
        $user = Auth::user();
        $digtal_orders = Order::where('customer_id', $user->id)->digitalOrder()->orderBy('id', 'DESC')->paginate(site_settings('pagination_number', 10));
        return view('user.digtal_order', compact('title', 'digtal_orders', 'user'));
    }

    public function followShop()
    {
        $title = translate('Follow shops');
        $user = User::with(['follower' => function ($q) {
            return $q->with(['seller' => function ($q) {
                return $q->with(['sellerShop']);
            }]);
        }])->where('id', Auth::user()->id)->first();
        return view('user.follow_shop', compact('title', 'user'));
    }

    public function trackOrder($id = null)
    {
        $title = translate('Tracking Order');
        $user = Auth::user();
        $order = null;
        $orderNumber = request()->input('order_number');

        if($id ) $orderNumber =  $id;

        if ($orderNumber) {
            $order = Order::with(['OrderDetails', 'OrderDetails.product','OrderDetails.product.seller','deliveryman' , 'deliveryman.ratings'])->where('order_id', $orderNumber)->first();
            if (!$order)    return back()->with('error', translate("Invalid Order"));
        }

        return view('frontend.track_order', compact('title', 'user', 'order', 'orderNumber'));
    }


    public function follow($id)
    {

        $seller = Seller::where('id', $id)->where('status', 1)->firstOrFail();
        $customer = Auth()->user();
        $follow = Follower::where('following_id', $customer->id)->where('seller_id', $seller->id)->first();

        if ($follow) {
            $follow->delete();
            return back()->with('success', translate("Unfollowed Successfully"));
        } else {
            $follow = new Follower();
            $follow->following_id = $customer->id;
            $follow->seller_id = $seller->id;
            $follow->save();
            return back()->with('success', translate("Followed Successfully"));
        }
    }



    public function profile(Request $request)
    {
        $title = translate('Profile');

        $countries = DB::table('countries')->get();
        $user      = Auth::user();

        return view('user.profile', compact('title', 'user', 'countries'));
    }


    public function profileUpdate(Request $request)
    {

        $user = Auth::user();

        $request->validate([
            'name'          => 'required|max:120',
            'username'      => 'required|unique:users,username,' . $user->id,
            'phone'         => 'required|unique:users,phone,' . $user->id,
            'address'       => 'required|max:250',
            'country_id'    => 'required|exists:countries,id',
            'city'          => 'required|max:250',
            'state'         => 'required|max:250',
            'zip'           => 'required|max:250',
            'latitude'      => 'required',
            'longitude'     => 'required'
        ]);

        $user->name         = $request->input('name');
        $user->last_name    = $request->input('last_name');
        $user->username     = $request->input('username');
        $user->phone        = $request->input('phone');
        $user->country_id = $request->country_id;

        $address = [
            'address'   => $request->input('address'),
            'country'   => $request->input('country'),
            'city'      => $request->input('city'),
            'state'     => $request->input('state'),
            'zip'       => $request->input('zip'),
            'latitude'  => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ];

        if ($request->hasFile('image')) {
            try {
                $removefile = $user->image ?: null;
                $user->image = store_file($request->image, file_path()['profile']['user']['path'], file_path()['profile']['user']['size'], $removefile);
            } catch (\Exception $exp) {
                return back()->with('error', translate("Image could not be uploaded."));
            }
        }
        $user->address = $address;
        $user->save();
        return back()->with('success', translate("Profile has been updated"));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'nullable',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->user();
        if ($user->password) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', translate("The password does not match!"));
            }
            $user->password = Hash::make($request->password);
            $user->save();
        } else {
            $user->password = Hash::make($request->password);
            $user->save();
        }
        return back()->with('success', translate("Password has been updated"));
    }


    public function productReview(Request $request)
    {


        $request->validate([
            'product_id' => 'required',
            'rate'       => 'required',
            'review'     => 'required|max:240',

        ]);
        $user = Auth::user();

        $productRating             = new ProductRating();
        $productRating->user_id    = $user->id;
        $productRating->product_id = $request->product_id;
        $productRating->rating     = $request->rate;
        $productRating->review     = $request->review;
        $productRating->save();

        return back()->with('success', translate("Thanks for your review. We appreciate your feedback"));
    }

    // user subscribe
    public function subscribe(Request $request)
    {
        $request->validate([
            'submit' => 'in:subscribe,unsubscribe'
        ]);

        $subscribe = Subscriber::where('email', auth()->user()->email)->first();

        if ($request->submit == 'subscribe') {
            $email = Auth::user()->email;
            if ($email) {
                $subscribe = new Subscriber();
                $subscribe->email = $email;
                $subscribe->save();
                return back()->with('success', translate("Successfully Subscribed"));
            }
        } else if ($request->submit == 'unsubscribe') {
            $subscribe->delete();
            return back()->with('success', translate("UnSubscribed Successfully"));
        } else {
            return back()->with('error', translate("Something went Wrong"));
        }
    }

    public function refund(Request $request)
    {
        $request->validate([
            "reason" => "required"
        ]);
        $flag = 1;
        $refund =  Refund::where('order_id', $request->order_id)->first();

        if ($refund) {
            if ($refund->refund_status == 'success') {
                $flag = 0;
                return back()->with('error', translate("You Already Have A Refund Request which is Succeed !"));
            } elseif ($refund->refund_status == 'pending') {
                $flag = 0;
                return back()->with('error', translate("You Already Have A Refund Request which is Under In Review !"));
            }
        }
        if ($flag == 1) {
            Refund::create([
                "user_id" => $request->user_id,
                "paymentID" => $request->payment_id,
                "trxID" => $request->order_id,
                "order_id" => $request->order_id,
                "method_id" => $request->method_id,
                "amount" => $request->amount,
                "reason" => $request->reason,
                "refund_status" => 'pending',
            ]);
            return back()->with('success', translate("Your Refund Request is Under Review Please Wait!!!"));
        }
    }



    public function deleteOrder(Request $request)
    {

        $request->validate([
            'id' => "required"
        ]);
        $order = Order::where('id', $request->id)->first();
        if ($order) {
            OrderDetails::where('order_id', $order->id)->delete();
            $order->delete();
            return back()->with('success', translate("Order Deleted"));
        }

        return back()->with('error', translate("Invalid Order"));
    }

    public function pay($id)
    {

        $title = translate('Order payment');
        $order = Order::with(['orderDetails','orderDetails.product'])->where('id', $id)
                         ->where('payment_status',Order::UNPAID)
                         ->firstOrfail();
        $user  = $this->user;


        $paymentMethods           = active_payment_methods();
        $manualPaymentMethods     = active_manual_payment_methods();


        return view('user.checkout',compact('title', 'order', 'paymentMethods', 'user', 'manualPaymentMethods'));




                 
                         


     
    }



    public function rewardPoints()
    {


        if(site_settings('club_point_system' ,0) == StatusEnum::false->status())  return back()->with('error',translate("Reward point system is currently inactive"));

    
        return view('user.reward_point',[
            'title' => translate("Reward point logs"),
            'user' => $this->user,
            'reward_overview' =>  (object)[
                                        'total'  => (int) RewardPointLog::date()
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
                                      ],
            'reward_logs' => RewardPointLog::with(['order','product'])->date()->filter()
                                            ->where('user_id',$this->user->id)
                                            ->paginate(site_settings('pagination_number',10))
        ]);
    }



    
    public function rewardPointShow($id)
    {


        if(site_settings('club_point_system' ,0) == StatusEnum::false->status())  return back()->with('error',translate("Reward point system is currently inactive"));

    
        return view('user.reward_show',[
            'title' => translate("Reward point show"),
            'user' => $this->user,
    
            'reward_log' => RewardPointLog::with(['order','product'])->date()->filter()
                                            ->where('user_id',$this->user->id)
                                            ->where('id',$id)
                                            ->firstOrfail()
        ]);
    }



    public function redeemPoint($id)
    {


        if(site_settings('club_point_system' ,0) == StatusEnum::false->status())  return back()->with('error',translate("Reward point system is currently inactive"));

    
       
        $rewardLog = RewardPointLog::with(['order','product'])
                                        ->where('user_id',$this->user->id)
                                        ->where('id',$id)
                                        ->pending()
                                        ->first();


        if(!$rewardLog)  return back()->with("error" ,translate('Invalid reward log'));
  


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
            'details'            => 'Balance added by redeeming '.$point. ' points',
        ]);
                
        $this->user->balance += $amount;
        $this->user->save();

        $rewardLog->status = RewardPointStatus::REDEEMED->value;

        $rewardLog->redeemed_at = Carbon::now();
        $rewardLog->save();


        return back()->with("success" ,translate('Redeemed successfully'));

    }






    public function deliveryManRating(Request $request)
    {


        $request->validate([
            'deliveryman_id'       => 'required:exists:delivery_men,id',
            'order_id'             => 'required:exists:orders,id',
            'rating'               => 'required|gt:0, max:5',
            'message'              => 'required|max:191',
        ]);
        $deliveryman = DeliveryMan::findOrfail($request->deliveryman_id);

        $order       = Order::where("id", $request->order_id)
                            ->whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                             $query->where(fn(Builder $query) : Builder => $query
                                            ->where('deliveryman_id',$deliveryman->id)
                                            ->orWhere('assign_by',$deliveryman->id)
                             )->where('status',DeliveryManOrder::ACCEPTED))
                            ->where("customer_id", $this->user->id)
                            ->firstOrfail();

                            
        if (DeliveryManRating::where('delivery_men_id', $deliveryman->id)
            ->where("user_id", $this->user->id)
            ->where("order_id", $order->id)
            ->exists()
        ) {

            return back()->with("error", translate('You already ratted this deliveryman'));
        }

        DeliveryManRating::create([
            'user_id'         => $this->user->id,
            'order_id'        => $order->id,
            'delivery_men_id' => $deliveryman->id,
            'rating'          => $request->input('rating'),
            'message'         => $request->input('message')
        ]);


        return back()->with("success", translate('Thanks for your feedback'));
    }
}
