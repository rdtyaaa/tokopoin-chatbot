<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Settings\NotificationType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Jobs\SendMailJob;
use App\Models\DeliveryMan;
use App\Models\DeliverymanEarningLog;
use App\Models\DeliveryManOrder;
use App\Traits\Notify;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\OrderDetails;
use App\Models\Transaction;
use App\Models\Seller;
use App\Models\Product;
use App\Models\GeneralSetting;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
class InhouseProductOrderController extends Controller
{

    use Notify;
    public function __construct()
    {
        $this->middleware(['permissions:view_order'])->only('index',"search",'placed','confirmed','processing','shipped','delivered','cancel','printInvoice');
        $this->middleware(['permissions:update_order'])->only('orderDetailStatusUpdate','orderStatusUpdate');
        $this->middleware(['permissions:delete_order'])->only('delete');
    }

    public function index()
    {
        $title  = translate("Inhouse all orders");
        $orders = Order::inhouseOrder()->search()->date()->physicalOrder()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function placed()
    {
        $title  = translate("Inhouse placed orders");
        $orders = Order::inhouseOrder()->search()->date()->physicalOrder()->placed()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function confirmed()
    {
        $title = translate("Inhouse confirmed orders");
        $orders = Order::inhouseOrder()->search()->date()->physicalOrder()->confirmed()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function processing()
    {
        $title = translate("Inhouse processing orders");
        $orders = Order::inhouseOrder()->physicalOrder()->search()->date()->processing()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function shipped()
    {
        $title = translate("Inhouse shipped orders");
        $orders = Order::inhouseOrder()->physicalOrder()->search()->date()->shipped()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function delivered()
    {
        $title = translate("Inhouse delivered orders");
        $orders = Order::inhouseOrder()->physicalOrder()->search()->date()->delivered()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function cancel()
    {
        $title = translate('Inhouse cancel orders');
        $orders = Order::inhouseOrder()->physicalOrder()->search()->date()->cancel()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }


    public function return()
    {
        $title = translate('Inhouse return orders');
        $orders = Order::inhouseOrder()->physicalOrder()->search()->date()->return()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }


    public function pending()
    {
        $title = translate('Inhouse pending orders');
        $orders = Order::inhouseOrder()->physicalOrder()->search()->date()->pending()->orderBy('id', 'DESC')->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product','paymentMethod')->paginate(site_settings('pagination_number',10))->appends(request()->all());;
        return view('admin.order.index', compact('title', 'orders'));
    }


    public function details($id)
    {
        $title = translate("Inhouse order details");
        $order = Order::with(['paymentMethod'])->inhouseOrder()->physicalOrder()->where('id', $id)->firstOrFail();

        $orderDeatils = OrderDetails::where('order_id', $order->id)->inhouseOrderProduct()->with('product')->get();

        $orderStatus  = OrderStatus::where('order_id', $id)->latest()->get();

        $deliverymen  = DeliveryMan::where('status', StatusEnum::true->status())->get();

        return view('admin.order.details', compact('title', 'order', 'orderDeatils', 'orderStatus' , 'deliverymen'));
    }

    public function search(Request $request, $scope)
    {
        $request->validate([
            'searchFilter'=>'required',
        ]);

        if($request->option_value == 'Select Menu'){
            return back()->with('error',translate("Please Select A Value Form Select Box"));
        }
        $search = $request->searchFilter;
        $title = "Inhouse order search by -" . $search;
        $orders = Order::inhouseOrder()->physicalOrder();

        if($request->option_value == 'order_number'){
            $orders->Where('order_id', '=', $search);
        }
        if($request->option_value == 'customer'){
            $orders->whereHas('customer', function($q) use ($search){
                    $q->where('name','like',"%$search%");
                });
        }
        if($request->option_value == 'Amount'){
            $orders->Where('amount', '=', $search);
        }
        if ($scope == 'placed') {
            $orders = $orders->placed();
        }elseif($scope == 'confirmed'){
            $orders = $orders->confirmed();
        }elseif($scope == 'processing'){
            $orders = $orders->processing();
        }elseif($scope == 'shipped'){
            $orders = $orders->shipped();
        }elseif($scope == 'delivered'){
            $orders = $orders->delivered();
        }elseif($scope == 'cancel'){
            $orders = $orders->cancel();
        }
        $orders = $orders->orderBy('id','desc')->with('customer')->paginate(site_settings('pagination_number',10));
     return view('admin.order.index', compact('title', 'orders', 'search'));
    }

    public function dateSearch(Request $request, $scope)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);
        $searchDate = explode('-',$request->date);
        $firstDate = $searchDate[0];
        $lastDate = $searchDate[1];
        $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
        if ($firstDate && !preg_match($matchDate,$firstDate)) {
            return back()->with('error',translate("Invalid order search date format"));
        }
        if ($lastDate && !preg_match($matchDate,$lastDate)) {
            return back()->with('error',translate("Invalid order search date format"));
        }
        if ($firstDate) {
            $orders = Order::inhouseOrder()->physicalOrder()->whereDate('created_at',Carbon::parse($firstDate));
        }
        if ($lastDate){
            $orders = Order::inhouseOrder()->physicalOrder()->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
        }
        if ($scope == 'placed') {
            $orders = $orders->placed();
        }elseif($scope == 'confirmed'){
            $orders = $orders->confirmed();
        }elseif($scope == 'processing'){
            $orders = $orders->processing();
        }elseif($scope == 'shipped'){
            $orders = $orders->shipped();
        }elseif($scope == 'delivered'){
            $orders = $orders->delivered();
        }elseif($scope == 'cancel'){
            $orders = $orders->cancel();
        }
        $orders = $orders->orderBy('id','desc')->with('customer')->paginate(site_settings('pagination_number',10));
        $searchDate = $request->date;
        $title = translate('Orders search by') . $searchDate;
        return view('admin.order.index', compact('title','orders','searchDate'));
    }

    public function printInvoice($id, $type)
    {
        $title = translate('Print invoice');
        $order = Order::inhouseOrder()->physicalOrder()->where('id',$id)->firstOrFail();
        if($type =="inhouse"){
            $orderDeatils = OrderDetails::where('order_id', $order->id)->inhouseOrderProduct()->with('product')->get();
        }else{
            $orderDeatils = OrderDetails::where('order_id', $order->id)->sellerOrderProduct()->with('product')->get();
        }

        return view('admin.order.print', compact('title', 'order', 'orderDeatils'));
    }


    public function orderStatusUpdate(Request $request, $id='')
    {

        if($id=='') $id = $request->order_id;

        if($request->payment_status!='' && $request->status==''){
            $datavalidate = [
                'payment_status' => 'required|in:1,2',
            ];
        }elseif($request->payment_status == '' && $request->status!=''){
            $datavalidate = [
                'status' => 'required|in:0,1,2,3,4,5,6,7',
            ];
        }else{
            $datavalidate = [
                'payment_status' => 'required|in:1,2',
                'status' => 'required|in:0,1,2,3,4,5,6,7',
            ];
        }

        $this->validate($request, $datavalidate);


        DB::transaction(function () use ($request ,$id){
            $order = Order::with(['deliveryman','customer'])->where('id', $id)->firstOrFail();


            if($order->status == Order::DELIVERED) return back()->with('error',translate('This order has already been delivered'));
            if($order->status == Order::RETURN) return back()->with('error',translate('This order has already been Returned'));
    
            if($request->payment_status){
                $order->payment_status = $request->payment_status;
            }
            if($request->status){
                $order->status = $request->status;
                if($order->status == 5){
                    $order->payment_status = Order::PAID;
    
                    if(@$order->customer && site_settings('club_point_system') == StatusEnum::true->status()){
                        WalletRecharge::generatePointLog(@$order->customer ,$order);
                    }
    
            
                    if($order->deliveryManOrder){
    
                        $deliveryLog = @$order->deliveryManOrder;
    
                  
                        $deliveryMan = @$order->deliveryManOrder->deliveryMan;
    
                        if($deliveryMan && $order->deliveryManOrder->status == DeliveryManOrder::DELIVERED){
    
                            $transaction = Transaction::create([
                                'deliveryman_id' => $deliveryMan->id,
                                'amount'          => $order->amount,
                                'post_balance'     => $deliveryMan->order_balance,
                                'transaction_type' => Transaction::PLUS,
                                'transaction_number' => trx_number(),
                                'details' => 'Order balance added for order number '.$order->order_id,
                            ]);
        
        
                            $deliveryMan->order_balance +=$order->amount;
        
                            $deliveryMan->save();
        
        
                            if($deliveryLog->amount > 0){
        
                                $transaction = Transaction::create([
                                    'deliveryman_id'     => $deliveryMan->id,
                                    'amount'             => $deliveryLog->amount ,
                                    'post_balance'       => $deliveryMan->balance,
                                    'transaction_type'   => Transaction::PLUS,
                                    'transaction_number' => trx_number(),
                                    'details' => 'Order charge added for order  '.$order->order_id,
                                ]);
        
                                $deliveryMan->balance +=$deliveryLog->amount;
                                $deliveryMan->save();
        
                                DeliverymanEarningLog::create([
                                        'deliveryman_id'     => $deliveryMan->id,
                                        'amount'             => $deliveryLog->amount,
                                        'order_id'           => $order->id,
                                        'details'            => 'Order charge added for order  '.$order->order_id
                                ]);
        
                            }
    
                            #CLUB POINT CALCULATION
                            if(site_settings('deliveryman_club_point_system') == StatusEnum::true->status()){
                                WalletRecharge::generateDeliveryManPointLog(@$deliveryMan ,$order);
                            }
    
                        }
                        $order->deliveryManOrder->status = DeliveryManOrder::DELIVERED;
                        $order->deliveryManOrder->save();
                    }
                }
                elseif($request->status == Order::RETURN){
                    $order->payment_status = Order::UNPAID;
                    $order->save();
                }
            }
    
            foreach ($order->orderDetails as $key => $value) {
    
                
                $commission  = 0;
                if(site_settings('seller_commission_status') ==  StatusEnum::true->status()){
                    $commission  = (($value->total_price * site_settings('seller_commission'))/100);
                }
    
    
                if($request->status == Order::DELIVERED){
    
                    $finalAmount = $value->total_price - $commission;
                    $product = Product::find($value->product_id);
                    if($product->seller_id){
                        $seller           = Seller::where('id',$product->seller_id)->first();
                        $seller->balance += $finalAmount;
                        $seller->save();
                        
                        $transaction = Transaction::create([
                            'seller_id' => $seller->id,
                            'amount' => $finalAmount,
                            'post_balance' => $seller->balance,
                            'transaction_type' => Transaction::PLUS,
                            'transaction_number' => trx_number(),
                            'details' => 'Amount added for this order '.$order->order_id,
                        ]);
                    }
                }
    
                $value->status = $request->status;
                $value->save();
    
            }
            $order->save();
    
            $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
            $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
            $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
            $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;
    
    
            // notify user
            $mailCode = [
                'order_number'     => $order->order_id,
                'time'             => Carbon::now(),
                'payment_status'   => $order->payment_status == Order::PAID ? 'Paid' :"Unpaid",
                'amount'           => show_amount($order->amount),
                'customer_phone'           => @$phone ?? 'N/A',
                'customer_email'           => @$email?? 'N/A',
                'customer_name'            => @$first_name ?? "N/A",
                'customer_address'         => @$address ?? 'N/A',
            ];
    
            $notificationFor = (object)[
                "first_name" => $first_name,
                "email" =>   $email,
            ];
    
            $user = auth()->user() ? auth()->user() :$notificationFor;
    
            if($order->status == Order::CANCEL){
                SendMailJob::dispatch($user,'ORDER_CANCEL',$mailCode);
            }
            elseif($order->status == Order::DELIVERED){
                SendMailJob::dispatch($user,'ORDER_DELIVERED',$mailCode);
            }
            elseif($order->status == Order::CONFIRMED){
                SendMailJob::dispatch($user,'ORDER_CONFIRMED',$mailCode);
            }
    
            $orderStatus = new OrderStatus();
            $orderStatus->order_id        = $id;
            $orderStatus->payment_status  = $request->status == 5 ? Order::PAID : $request->payment_status;
    
            if($request->payment_note!=''){
                $orderStatus->payment_note    = $request->payment_note;
            }
            $orderStatus->delivery_status = $request->status;
            if($request->delivery_note!=''){
                $orderStatus->delivery_note   = $request->delivery_note;
            }
            $orderStatus->save();
    
    
            #FIREBASE NOTIFICATIONS
    
    
            # Send delivery man firebase notification
            if(@$order->deliveryManOrder && @$order->deliveryManOrder->deliveryMan){
    
                $deliveryLog = @$order->deliveryManOrder;
                $deliveryMan = @$order->deliveryManOrder->deliveryMan;
    
                if($deliveryMan && $deliveryMan->fcm_token && $deliveryMan->enable_push_notification == 1){
    
                    $payload = (object) [
                        "title"        => translate('Order Status'),
                        "message"      => translate('Order status updated'),
                        "order_number" => $order->order_id,
                        "order_id"     => $order->id,
                        "order_uid"    => $order->uid,
                        "type"         => NotificationType::ORDER->value,
                    ];
                    $this->fireBaseNotification($deliveryMan->fcm_token, $payload );
    
                }
    
            }
    
            #Send customer notification
            if($order->customer &&  $order->customer->fcm_token){
                $payload = (object) [
                    "title"        => translate('Order Status'),
                    "message"      => translate('Order status updated'),
                    "order_number" => $order->order_id,
                    "order_id"     => $order->id,
                    "order_uid"    => $order->uid,
                    "type"         => NotificationType::ORDER->value,
                ];
                $this->fireBaseNotification($order->customer->fcm_token, $payload );
            }

        });

  


        return back()->with('success',translate('Order status has been updated'));
    }


    public function orderDetailStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:order_details,id',
            'status' => 'required|in:0,1,2,3,4,5,6,7',
        ]);
        $orderDeatils = OrderDetails::with(['product','product.seller'])->where('id', $request->id)->firstOrfail();

        $order = Order::where('id',  $orderDeatils->order_id)->firstOrfail();


        if($orderDeatils->status == Order::DELIVERED) return back()->with('error',translate('This order has already been delivered'));
        if($orderDeatils->status == Order::RETURN) return back()->with('error',translate('This order has already been Returned'));

        $orderDeatils->status = $request->status;

        
        $orderDeatils->save();

        
        if($orderDeatils->product &&  $orderDeatils->product->seller ){

            $product = $orderDeatils->product;
            $commission  = 0;
            if(site_settings('seller_commission_status') ==  StatusEnum::true->status()) $commission  = (($orderDeatils->total_price * site_settings('seller_commission'))/100);

            $seller = $orderDeatils->product->seller;
            $finalAmount = $orderDeatils->total_price - $commission;

            if($orderDeatils->status == Order::DELIVERED) {
                $seller->balance += $finalAmount;
                $seller->save();
                $transaction = Transaction::create([
                    'seller_id' => $seller->id,
                    'amount' => $finalAmount,
                    'post_balance' => $seller->balance,
                    'transaction_type' => Transaction::PLUS,
                    'transaction_number' => trx_number(),
                    'details' => 'Amount added for this order '.$order->order_id,
                ]);
            }
            
        }
    

        return back()->with('success',translate('Order product status has been updated'));
    }

    public function delete($id)
    {
        $order = Order::with(['orderDetails'])->where('id',$id)->first();
        $order->orderDetails()->delete();
        $order->delete();
        return back()->with('success',translate('Order Deleted Successfully'));
    }


    public function assign(Request $request){


        $request->validate([
            'id'                  => "required|exists:orders,id",
            'delivery_man_id'     => "required|exists:delivery_men,id",
            'delivery_man_charge' => "nullable|gt:-1|numeric",
            'note' => "nullable",
            'pickup_address' => "required",
        ]);


        DB::transaction(function () use ($request){
        
            $order = Order::with(['deliveryman','customer'])
                                    ->where('status','!=',Order::DELIVERED)
                                    ->where('id', $request->id)
                                    ->firstOrFail();

            $deliveryMan = DeliveryMan::findOrfail($request->delivery_man_id);
            
            $deliverymanOrder = DeliveryManOrder::with(['deliveryMan'])->where("order_id",$order->id)->first();


            $timeline = [
                            'assign' => [
                                'action_by' => 'Superadmin',
                                'time'      => Carbon::now(),
                                'details'   => translate("Assign order by superadmin to "). $deliveryMan->first_name,
                            ]
                        ];


            $createNew = true;

            if($deliverymanOrder) {
                $createNew = false;
                if($deliverymanOrder->deliveryman_id != $deliveryMan->id){
                    $createNew = true;
                    $timeline ['unassign'] = [
                        'action_by' => 'Superadmin',
                        'time'      => Carbon::now(),
                        'details'   => translate("Order unassigned form "). @$deliverymanOrder->deliveryMan->first_name,
                    ];

                    $deliverymanOrder->delete();  

                }

            }




            $deliverymanOrder = $createNew  ? new DeliveryManOrder() : $deliverymanOrder;
            $deliverymanOrder->order_id        = $order->id;
            $deliverymanOrder->deliveryman_id  = $deliveryMan->id;
            $deliverymanOrder->pickup_location = $request->pickup_address;
            $deliverymanOrder->note            = $request->note;
            $deliverymanOrder->amount          = $request->delivery_man_charge ?? 0;

            if($createNew){

                $deliverymanOrder->status          = site_settings('deliveryman_assign_cancel') == StatusEnum::true->status()
                                                            ? DeliveryManOrder::PENDING 
                                                            : DeliveryManOrder::ACCEPTED; 
                 $deliverymanOrder->time_line        = $timeline;

            }



           $deliverymanOrder->save();

            # Send delivery man firebase notification
            if(@$deliveryMan && @$deliveryMan->fcm_token){
                if(@$deliveryMan->enable_push_notification == 1){
                        $payload = (object) [
                            "title"        => translate('Order'),
                            "message"      => translate('You have a new assign order'),
                            "order_number" => $order->order_id,
                            "order_id"     => $order->id,
                            "order_uid"    => $order->uid,
                            "type"         => NotificationType::ORDER->value,
                        ];

                

                    $this->fireBaseNotification(@$deliveryMan->fcm_token, $payload );
                }
            }

            #Send customer notification

            if($order->customer &&  $order->customer->fcm_token){

                $payload = (object) [
                    "title"        => translate('Order'),
                    "message"      => translate('Your order has been assign to ').$order->deliveryman 
                                                                                    ? @$order->deliveryman->first_name 
                                                                                    : "Deliveryman",
                    "order_number" => $order->order_id,
                    "order_id"     => $order->id,
                    "order_uid"    => $order->uid,
                    "type"         => NotificationType::ORDER->value,
                ];
                $this->fireBaseNotification($order->customer->fcm_token, $payload );
            }
        });


        return back()->with('success',translate("Deliveryman assigned"));

    }
}
