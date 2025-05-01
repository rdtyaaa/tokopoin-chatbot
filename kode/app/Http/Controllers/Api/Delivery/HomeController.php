<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Enums\KYCStatus;
use App\Enums\Settings\GlobalConfig;
use App\Enums\StatusEnum;
use App\Http\Controllers\Api\Seller\WithdrawController;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CurrencyCollection;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\Deliveryman\ConfigResource;
use App\Http\Resources\Deliveryman\DeliveryManCollection;
use App\Http\Resources\Deliveryman\DeliverymanEarningCollection;
use App\Http\Resources\Deliveryman\DeliveryManOrderCollection;
use App\Http\Resources\Deliveryman\DeliveryManResource;
use App\Http\Resources\Deliveryman\RewardpointCollection;
use App\Http\Resources\LanguageCollection;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PagesCollection;
use App\Http\Resources\Seller\KycLogCollection;
use App\Http\Resources\Seller\KycLogResource;
use App\Http\Resources\Seller\OrderCollection;
use App\Http\Resources\Seller\OrderResource;
use App\Http\Resources\Seller\TransactionCollection;
use App\Http\Resources\Seller\WithdrawCollection;
use App\Http\Services\Deliveryman\DeliverymanService;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Models\DeliverymanEarningLog;
use App\Models\DeliveryManOrder;
use App\Models\DeliveryManRating;
use App\Models\KycLog;
use App\Models\Language;
use App\Models\Order;
use App\Models\PageSetup;
use App\Models\PaymentLog;
use App\Models\RewardPointLog;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Settings\NotificationType;
use App\Traits\Notify;
class HomeController extends Controller
{

    use Notify;
    protected ? DeliveryMan $deliveryman;

    public function __construct(protected DeliverymanService $deliverymanService){

        $this->middleware(function ($request, $next) {
            $this->deliveryman = auth()->guard('delivery_man:api')->user()?->load(['ratings','ratings.user','ratings.order','refferedBy']);
            return $next($request);
        });
    }

    /**
     * Get configuration
     *
     * @return JsonResponse
     */
    public function config() :JsonResponse {

        $languages          = Language::active()->get();
        $defaultLanguage    = Language::default()->first();
        $currencies         = Currency::active()->get();
        $pages              = PageSetup::active()->get();

        $faqs  = collect(site_settings('delivery_faq',null) 
                                ? json_decode(site_settings('delivery_faq')) 
                                : []);

 

        $countries          = Country::visible()->with(['states'=>fn(HasMany $q) => $q->visible(),'states.cities'=>fn(HasMany $q) => $q->visible()])->get();

        return api([

            "countries"               => new CountryCollection($countries),
            "phone_codes"             => GlobalConfig::TELEPHONE_CODES,
            'config'                  => new ConfigResource(general_setting()),
            'languages'               => new LanguageCollection($languages),
            'default_language'        => new LanguageResource($defaultLanguage),
            'currency'                => new CurrencyCollection($currencies),
            'default_currency'        => new CurrencyResource(default_currency()),

            'image_format'               => file_format(),
            'file_format'                => file_format('file'),

            'delevary_status'        => Order::delevaryStatus(),

            'pages'                   => new PagesCollection($pages),
            'faqs'                    => ($faqs->values()),
            'kyc_config'                     => json_decode(site_settings('deliveryman_kyc_settings')),
            

        ])->success(__('response.success'));

    }




    /**
     * Get dashboard overview
     *
     * @return JsonResponse
     */
    public function home() : JsonResponse {


        $latest_orders = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                     $query->where(fn(Builder $query) : Builder => $query
                                                        ->where('deliveryman_id',$this->deliveryman->id)
                                                   )->where('status','!=',DeliveryManOrder::PENDING))
                                    ->with(['customer',
                                            'orderStatus',
                                            'log',
                                            'orderDetails',
                                            'shipping',
                                            'paymentMethod',
                                            'orderDetails.product',
                                            'deliveryman',
                                            'orderRatings',
                                            'orderRatings.user',
                                            'billingAddress.user',
                                            'deliveryManOrder'
                                           ])
                                    ->physicalOrder()
                                    ->orderByDesc(function($query) {
                                        $query->select('created_at')
                                              ->from('delivery_man_orders')
                                              ->whereColumn('delivery_man_orders.order_id', 'orders.id')
                                              ->orderBy('created_at', 'desc') // Ensure the latest assignment is selected
                                              ->limit(1);
                                    })
                                    ->search()
                                    ->date()
                                    ->take(4)
                                    ->get();


      


        $requested_orders = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                $query->where(fn(Builder $query) : Builder => $query
                                                                ->where('deliveryman_id',$this->deliveryman->id)
                                                            )
                                                    ->where('status',DeliveryManOrder::PENDING)
                                                )
                                           ->with(['customer',
                                                'orderStatus',
                                                'log',
                                                'orderDetails',
                                                'shipping',
                                                'paymentMethod',
                                                'orderDetails.product',
                                                'deliveryman',
                                                'orderRatings',
                                                'orderRatings.user',
                                                'billingAddress.user',
                                                'deliveryManOrder'
                                                ])
                                        ->physicalOrder()
                                        ->whereNotIn('status',[Order::DELIVERED,Order::CANCEL])
                                        ->orderByDesc(function($query) {
                                            $query->select('created_at')
                                                  ->from('delivery_man_orders')
                                                  ->whereColumn('delivery_man_orders.order_id', 'orders.id')
                                                  ->orderBy('created_at', 'desc') 
                                                  ->limit(1);
                                        })
                                        ->search()
                                        ->date()
                                        ->take(4)
                                        ->get();


        $overview = [


            "total_success_withdraw"   => api_short_amount(Withdraw::where('status', PaymentLog::SUCCESS)
                                                                ->whereNull('seller_id')
                                                                ->where('deliveryman_id',$this->deliveryman->id)
                                                                ->sum('amount')),


            "total_pending_withdraw"   => api_short_amount(Withdraw::pending()
                                                                ->whereNull('seller_id')
                                                                ->where('deliveryman_id',$this->deliveryman->id)
                                                                ->sum('amount')),


            "total_rejected_withdraw"   => api_short_amount(Withdraw::rejected()
                                                                ->whereNull('seller_id')
                                                                ->where('deliveryman_id',$this->deliveryman->id)
                                                                ->sum('amount')),
        ];



        $overview['total_order']    =    Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                     $query->where(fn(Builder $query) : Builder => $query
                                                     ->where('deliveryman_id',$this->deliveryman->id)
                                                     ->orWhere('assign_by',$this->deliveryman->id))
                                                            ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()     
                                                    ->count();
        
        $overview['requested_order']    =    Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                $query->where(fn(Builder $query) : Builder => $query
                                                ->where('deliveryman_id',$this->deliveryman->id))
                                                        ->where('status',DeliveryManOrder::PENDING))
                                                ->physicalOrder()
                                                ->latest()     
                                                ->count();


        $overview['total_placed_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                        $query->where(fn(Builder $query) : Builder => $query
                                                        ->where('deliveryman_id',$this->deliveryman->id)
                                                        ->orWhere('assign_by',$this->deliveryman->id))
                                                                ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->placed()
                                                    ->count();
                            
        $overview['total_return_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                     $query->where(fn(Builder $query) : Builder => $query
                                                     ->where('deliveryman_id',$this->deliveryman->id)
                                                     ->orWhere('assign_by',$this->deliveryman->id))
                                                           ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->return()
                                                    ->count();


                                    
        
        $overview['total_shipped_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                        $query->where(fn(Builder $query) : Builder => $query
                                                        ->where('deliveryman_id',$this->deliveryman->id)
                                                        ->orWhere('assign_by',$this->deliveryman->id))
                                                              ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->shipped()
                                                    ->count();

        $overview['total_cancel_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                        $query->where(fn(Builder $query) : Builder => $query
                                                        ->where('deliveryman_id',$this->deliveryman->id)
                                                        ->orWhere('assign_by',$this->deliveryman->id))
                                                            ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->cancel()
                                                    ->count();

        $overview['total_delivered_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                    $query->where(fn(Builder $query) : Builder => $query
                                                    ->where('deliveryman_id',$this->deliveryman->id)
                                                    ->orWhere('assign_by',$this->deliveryman->id))
                                                        ->where('status',DeliveryManOrder::DELIVERED))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->delivered()
                                                    ->count();

        $overview['total_confirmed_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                    $query->where(fn(Builder $query) : Builder => $query
                                                    ->where('deliveryman_id',$this->deliveryman->id)
                                                    ->orWhere('assign_by',$this->deliveryman->id))
                                                        ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->confirmed()
                                                    ->count();

        $overview['total_processing_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                            $query->where(fn(Builder $query) : Builder => $query
                                                            ->where('deliveryman_id',$this->deliveryman->id)
                                                            ->orWhere('assign_by',$this->deliveryman->id))
                                                                ->where('status','!=',DeliveryManOrder::PENDING))
                                                    ->physicalOrder()
                                                    ->latest()
                                                    ->processing()
                                                    ->count();

       $overview['earning_log'] = collect(sort_by_month(DeliverymanEarningLog::where('deliveryman_id', $this->deliveryman->id)
                                                            ->selectRaw("MONTHNAME(created_at) as months, SUM(amount) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray()))->map(function($total){
                                                                return api_short_amount((double)@$total ?? 0);
                                                            })->all();


        


        return api(
            [
                "delivery_man"       => new DeliveryManResource($this->deliveryman),
                'latest_orders'      => new OrderCollection($latest_orders),
                'requested_orders'   => new OrderCollection($requested_orders),
                'overview'           => $overview
            ]
            )->success(__('response.success'));
    }



        /**
     * Get  orders
     *
     * @return JsonResponse
     */
    public function orders() : JsonResponse {

        $orderStatus      = request()->input('status');

        $delevaryStatuses = Order::delevaryStatus();

        $orders =  Order::with(
              [
                          'customer',
                          'orderStatus',
                          'log',
                          'orderDetails',
                          'shipping',
                          'paymentMethod',
                          'orderDetails.product',
                          'deliveryman',
                          'orderRatings',
                          'orderRatings.user',
                          'billingAddress.user',
                          'deliveryManOrder'
                          ])->whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                         $query->where(fn(Builder $query) : Builder => $query
                                             ->where('deliveryman_id',$this->deliveryman->id)
                                              )->where('status','!=',DeliveryManOrder::PENDING))
                                            ->physicalOrder()    
                                            ->when( $orderStatus && Arr::exists($delevaryStatuses,$orderStatus) , fn(Builder $q) => $q->where("status",Arr::get(   $delevaryStatuses,$orderStatus)))
                                            ->search()
                                            ->date()
                                            ->orderByDesc(function($query) {
                                                $query->select('created_at')
                                                      ->from('delivery_man_orders')
                                                      ->whereColumn('delivery_man_orders.order_id', 'orders.id')
                                                      ->orderBy('created_at', 'desc') 
                                                      ->limit(1);
                                            })
                                            ->paginate(site_settings('pagination_number',10))
                                            ->appends(request()->all());


        return api([
            'orders'   => new OrderCollection($orders)
        ])->success(__('response.success'));

    }




    /**
     * Get seller transaction
     *
     * @return JsonResponse
     */
    public function transactions() : JsonResponse {

        $transactions =  Transaction::deliverymen()
                                ->search()
                                ->date()
                                ->where('deliveryman_id', $this->deliveryman->id)
                                ->latest()
                                ->paginate(site_settings('pagination_number',10))
                                ->appends(request()->all());

        return api([
            'transactions'   => new TransactionCollection($transactions)
        ])->success(__('response.success'));

    }







     /**
     * Get  requested orders
     *
     * @return JsonResponse
     */
    public function requestedOrders() : JsonResponse {

        $orderStatus      = request()->input('status');

        $delevaryStatuses = Order::delevaryStatus();

        $orders =  Order::with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user'])
                                            ->whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                        $query->where(fn(Builder $query) : Builder => $query
                                                          ->where('deliveryman_id',$this->deliveryman->id))
                                                             ->where('status',DeliveryManOrder::PENDING))
                                            ->physicalOrder()
                                            ->latest()
                                            ->when( $orderStatus && Arr::exists($delevaryStatuses,$orderStatus) , fn(Builder $q) => $q->where("status",Arr::get(   $delevaryStatuses,$orderStatus)))
                                            ->search()
                                            ->date()
                                            ->latest()
                                            ->paginate(site_settings('pagination_number',10))
                                            ->appends(request()->all());


        return api([
            'orders'   => new OrderCollection($orders)
        ])->success(__('response.success'));

    }



    /**
     * Get  earnings
     *
     * @return JsonResponse
     */
    public function earnings() : JsonResponse {

        $earningLogs = DeliverymanEarningLog::where('deliveryman_id', $this->deliveryman->id)
                                 ->date()
                                 ->filter()
                                 ->with(['order','order.orderDetails','order.orderDetails.product'])
                                 ->latest()
                                 ->paginate(site_settings('pagination_number',10));

        return api([
            'earning_logs'   => new DeliverymanEarningCollection($earningLogs)
        ])->success(__('response.success'));

    }


    public function orderDetails($order_number) {

             $order = Order::physicalOrder()
                             ->with(['customer','orderStatus','log','orderDetails','shipping','paymentMethod','orderDetails.product','deliveryman','orderRatings','orderRatings.user','billingAddress.user'])

                            ->where('order_id', $order_number)
                            ->first();

            if(! $order) return [ 'status'  => false, 'message' => translate("Invalid order number")];

            return api([
                'orders'   => new OrderResource($order)
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

        $this->deliveryman->fcm_token = $request->input('fcm_token');
        $this->deliveryman->save();

        return api(
            [
                'message'   => translate("Token updated")
            ])->success(__('response.success'));

    }




    /**
     * Summary of activeStatusUpdate
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeStatusUpdate(Request $request) :JsonResponse{


        $validator = Validator::make($request->all(),[
            'status' => 'required|in:online,offline',
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


        $onlineStatus = $request->input("status") == 'online' ? 1 : 0;


        $this->deliveryman->is_online = $onlineStatus;

        $this->deliveryman->saveQuietly();

        return api(
            [
                'message'   => translate("Status updated")
            ])->success(__('response.success'));


    }


     /**
     * Summary of activeStatusUpdate
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pushNotificationStatusUpdate(Request $request) :JsonResponse{

        $validator = Validator::make($request->all(),[
            'status' => 'required|in:1,0',
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $this->deliveryman->enable_push_notification =$request->input("status");

        $this->deliveryman->saveQuietly();

        return api(
            [
                'message'   => translate("Status updated")
            ])->success(__('response.success'));

    }





      /**
     * KYC application
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function  kycApplication(Request $request) :JsonResponse{


        if($this->deliveryman->is_kyc_verified == 1) return api(['errors'=>translate('Already verified')])->fails(__('response.fail'));

        $pendingKycs =  KycLog::where("deliveryman_id",$this->deliveryman->id)->pending()->count();

        if($pendingKycs > 0) return api(['errors'=>'You already have a pending KYC request, Please wait for our confirmation'])->fails(__('response.fail'));


        $rules = [];
        $message = [];

        $kycSettings     = !is_array(site_settings('deliveryman_kyc_settings',[])) 
                                    ? json_decode(site_settings('deliveryman_kyc_settings',[]),true) 
                                    : [];

        foreach( $kycSettings as $fields){

                $required =null;
                if($fields['required'] == '1'){
                   $required ="required";
                }
                if($fields['type'] == 'file'){
                    $rules['kyc_data.files.'.$fields['name']] = [$required, new FileExtentionCheckRule(file_format())];
                }
                elseif($fields['type'] == 'email'){
                    $rules['kyc_data.'.$fields['name']] = [$required,'email'];
                    $message['kyc_data.'.$fields['name'].".email"] = ucfirst($fields['name']).translate(' Feild Is Must Be Contain a Valid Email');
                }
                else{
                    $rules['kyc_data.'.$fields['name']] = [$required];
                }
                $message['kyc_data.'.$fields['name'].".required"] = ucfirst($fields['name']).translate(' Feild Is Required');
            
        }

        $validator = Validator::make($request->all(),$rules,$message);
        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        
        $kycLog =   DB::transaction(function() use ($request  ) {

                        $customData = (Arr::except($request['kyc_data'],['files']));

                        $kycLog                  = new KycLog();
                        $kycLog->deliveryman_id       = $this->deliveryman->id;
                        $kycLog->status          = KYCStatus::REQUESTED->value;
                        $kycLog->save();
                        $files =  [];
                        if(isset($request["kyc_data"] ['files'])){
                            foreach($request["kyc_data"] ['files'] as $key => $file){
                                try{
                                    $files [$key] = store_file($file,file_path()['seller_kyc']['path']);
                                }catch (\Exception $exp) {
                                }
                            }
                        }

                        if(count($files) > 0 ) $customData['files'] = $files;
                        $kycLog->custom_data = $customData;
                        $kycLog->save();
                        return $kycLog ;
       });


    return api(
        [
            'message'   => translate("KYC application submitted! Verification in progress. We will notify you upon completion. Thank you for your patience"),
            'log' => new KycLogResource($kycLog)
        ])->success(__('response.success'));

    }




    /**
     * KYC application
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function  kycLog() :JsonResponse{
        $logs =  KycLog::where("deliveryman_id",$this->deliveryman->id)->latest()->get();
        return api([ 
            'kyc_logs'          => new KycLogCollection($logs)

        ])->success(__('response.success'));

    }





    /**
     * Summary of getDeliverymen
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeliverymen(): JsonResponse{


        $deliveryMen = DeliveryMan::with(['country','ratings'])
                            ->latest()
                            ->where('id','!=',$this->deliveryman->id)
                            ->active()
                            ->lazyById(100,'id')
                            ->map(function($deliveryMan) : DeliveryMan{
                                $distance  = null;
                                if($deliveryMan->address && $this->deliveryman->address){
                                    $distance = calculateDistance($this->deliveryman->address,$deliveryMan->address);
                                }
                                $deliveryMan->distance = $distance;
                                $deliveryMan->distance_in_words = $distance 
                                                                    ? distanceInWords($distance) 
                                                                    : translate('nowhere near');
                                return $deliveryMan;
                            })->sortBy(function($deliveryMan) {
                                return is_null($deliveryMan->distance) ? INF : $deliveryMan->distance;
                            });
     



        return api([ 
            'delivery_men'          => new DeliveryManCollection($deliveryMen->values()->all())
        ])->success(__('response.success'));



    }





    /**
     * Summary of analytics
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics(): JsonResponse{



        $overview['total_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                        $query->where(fn(Builder $query) : Builder => $query
                                                            ->where('deliveryman_id',$this->deliveryman->id)
                                                            ->orWhere('assign_by',$this->deliveryman->id))
                                                            ->where('status','!=',DeliveryManOrder::PENDING))
                                                            ->physicalOrder()
                                                            ->latest()
                                                            ->count();

        $overview['total_delivered']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                        $query->where(fn(Builder $query) : Builder => $query
                                                        ->where('deliveryman_id',$this->deliveryman->id)
                                                        ->orWhere('assign_by',$this->deliveryman->id))
                                                        ->where('status',DeliveryManOrder::DELIVERED))
                                                        ->physicalOrder()
                                                        ->latest()
                                                        ->delivered()
                                                        ->count();


        $overview['pending_order']    = Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                $query->where(fn(Builder $query) : Builder => $query
                                                ->where('deliveryman_id',$this->deliveryman->id)
                                                ->orWhere('assign_by',$this->deliveryman->id))
                                                ->where('status',DeliveryManOrder::ACCEPTED))
                                                ->physicalOrder()
                                                ->latest()
                                                ->count();



         $deliveredOrderGraph          = (sort_by_month(Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                            $query->where(fn(Builder $query) : Builder => $query
                                                            ->where('deliveryman_id',$this->deliveryman->id)
                                                            ->orWhere('assign_by',$this->deliveryman->id))
                                                            ->where('status',DeliveryManOrder::DELIVERED))
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->delivered()
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray()));

         $pendingOrderGraph          = (sort_by_month(Order::whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                                            $query->where(fn(Builder $query) : Builder => $query
                                                            ->where('deliveryman_id',$this->deliveryman->id)
                                                            ->orWhere('assign_by',$this->deliveryman->id))
                                                            ->where('status',DeliveryManOrder::ACCEPTED))
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->whereNotIn('status',[Order::CANCEL,Order::DELIVERED,Order::RETURN])
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray()));



        $overview['order_graph'] = collect($deliveredOrderGraph)->map(function($value,$month) use ($pendingOrderGraph){

            return [
                $month => [
                    'delivered' => $value,
                    'pending'  => Arr::get($pendingOrderGraph,$month ,0),
                ]
            ];
        })->collapse()->all();



        $weeklyEarnings = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0,
        ];


        $earningsByWeek = DeliverymanEarningLog::where('deliveryman_id', $this->deliveryman->id)
                                                ->selectRaw("DAYNAME(created_at) as day, SUM(amount) as total")
                                                ->whereYear('created_at', date("Y"))
                                                ->whereMonth('created_at', date("m"))
                                                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                                ->groupBy('day')
                                                ->pluck('total', 'day')
                                                ->toArray();



        $lastTenYears = [];
        $currentYear = date('Y');

        for ($i = 0; $i < 10; $i++) {
            $year = $currentYear - $i;
            $lastTenYears[$year] = 0;
        }
        
        $earnings = DeliverymanEarningLog::where('deliveryman_id', $this->deliveryman->id)
                        ->selectRaw("YEAR(created_at) as year, SUM(amount) as total")
                        ->whereBetween('created_at', [now()->subYears(10)->startOfYear(), now()->endOfYear()])
                        ->groupBy('year')
                        ->pluck('total', 'year')
                        ->toArray();

               
        
        $lastTenYearsEarnings = collect($lastTenYears)->map(function($value , $key) use($earnings){
            return api_short_amount (Arr::get($earnings ,$key ,$value ));
        });


   

        $overview['earning_log'] = [
            'monthly' => collect(sort_by_month(DeliverymanEarningLog::where('deliveryman_id', $this->deliveryman->id)
                                            ->selectRaw("MONTHNAME(created_at) as months, SUM(amount) as total")
                                            ->whereYear('created_at', '=',date("Y"))
                                            ->groupBy('months')
                                            ->pluck('total', 'months')
                                            ->toArray()))->map(function($total){
                                                return api_short_amount((double)@$total ?? 0);
                                            })->all(),
            'weekly' => collect(array_merge($weeklyEarnings, $earningsByWeek))->map(function($total){
                                return api_short_amount((double)@$total ?? 0);
                            })->all(),

            'yearly' =>   $lastTenYearsEarnings,

      ];

        return api(['overview'=> $overview]
            )->success(__('response.success'));

                

    }


    /**
     * Summary of assignOrder
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignOrder(Request $request): JsonResponse{


        if(site_settings('order_assign') != StatusEnum::true->status()) return api(['errors'=> [translate("This module is not available")]])
        ->fails(__('response.fail'));  


        $validator = Validator::make($request->all(),[
            'order_id'         => 'required|exists:orders,id',
            'delivery_man_id'  => 'required|exists:delivery_men,id',
            'note' => 'nullable',
            'pickup_location' => 'required'
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));



        $deliveryman = DeliveryMan::active()
                                   ->where('id',$request->input('delivery_man_id'))
                                    ->first();


         if(!$deliveryman)  return api(['errors'=> [translate("Invalid deliveryman")]])
                                   ->fails(__('response.fail'));

        $order = Order::with(['customer'])
                                 ->whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                        $query->where(fn(Builder $query) : Builder => $query
                                                 ->where('deliveryman_id',$this->deliveryman->id)
                                                  ->orWhere('assign_by',$this->deliveryman->id))
                                ->whereIn('status',[DeliveryManOrder::PENDING , DeliveryManOrder::REJECTED]))
                                ->where('id',$request->input('order_id'))
                                ->whereNotIn('status',[Order::DELIVERED,Order::CANCEL,Order::RETURN])
                                ->first();



        if(!$order) return api(['errors'=> [translate("Order not found")]])
                                                        ->fails(__('response.fail'));
        if(!$order->deliveryManOrder) return api(['errors'=> [translate("Order Already assigned")]])
                                                        ->fails(__('response.fail'));




        $deliverymanOrder = $order->deliveryManOrder;


        
        if($deliverymanOrder->deliveryman_id ==  $deliveryman->id) return api(['errors'=> [translate("Already assigned")]])
        ->fails(__('response.fail'));


        $timeLine =  (array)$deliverymanOrder->time_line;

        
        $newTimeline =  [
            'transfer' => [
                'action_by' => $this->deliveryman->first_name,
                'time'      => Carbon::now(),
                'details'   =>  translate('Order transfer form ').$this->deliveryman->first_name." To ".$deliveryman->first_name,
            ]
        ];


        $updatedTimelineArray = array_merge($newTimeline, $timeLine);




        $deliverymanOrder->assign_by            = $this->deliveryman->id;
        $deliverymanOrder->deliveryman_id       = $deliveryman->id;

        
        $deliverymanOrder->pickup_location      = $request->pickup_location;
        $deliverymanOrder->note                 = $request->note;

        $deliverymanOrder->status               = site_settings('deliveryman_assign_cancel') == StatusEnum::true->status()
                                                                        ? DeliveryManOrder::PENDING 
                                                                        : DeliveryManOrder::ACCEPTED; 

                                                                        
        $deliverymanOrder->time_line        = $updatedTimelineArray;
        

        $deliverymanOrder->save();



          # Send delivery man firebase notification
          if(@$deliveryman && @$deliveryman->fcm_token){
            if($deliveryman->enable_push_notification == 1){
                    $payload = (object) [
                        "title"        => translate('Order'),
                        "message"      => translate('You have a new assign order'),
                        "order_number" => $order->order_id,
                        "order_id"     => $order->id,
                        "order_uid"    => $order->uid,
                        "type"         => NotificationType::ORDER->value,
                    ];

                $this->fireBaseNotification($deliveryman->fcm_token, $payload );
            }
        }


        return api(
            [
                'message'  => translate('Order assigned successfully'),
            ])->success(__('response.success'));



    }





    /**
     * Summary of handleRequestedOrder
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleRequestedOrder(Request $request): JsonResponse{

        if(site_settings('deliveryman_assign_cancel') != StatusEnum::true->status()) return api(['errors'=> [translate("This module is not available")]])
        ->fails(__('response.fail'));  


        $validator = Validator::make($request->all(),[
            'order_id'         => 'required|exists:orders,id',
            'status'           => 'required|in:'.DeliveryManOrder::ACCEPTED.",".DeliveryManOrder::REJECTED,
            'note'             => 'required'
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));



        $order = Order::with(['customer'])
                            ->whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                                $query->where(fn(Builder $query) : Builder => $query
                                            ->where('deliveryman_id',$this->deliveryman->id)
                                              ->orWhere('assign_by' ,$this->deliveryman->id))
                                                ->whereIn('status',[DeliveryManOrder::PENDING ,DeliveryManOrder::REJECTED]))
                                                ->where('id',$request->input('order_id'))
                                                ->whereNotIn('status',[Order::DELIVERED,Order::CANCEL,Order::RETURN])
                                                ->first();

        if(!$order) return api(['errors'=> [translate("Order not found")]])
                                ->fails(__('response.fail'));



        $deliveryManOrder = $order->deliveryManOrder;

        $timelineKey = 'rejected';

        $details = translate('Order pickup request declined by').$this->deliveryman->first_name;

        if( $request->input("status") == DeliveryManOrder::ACCEPTED){
            $timelineKey = 'accepted';
            $details = translate('Order pickup request accepted by').$this->deliveryman->first_name;
        }
        $newTimeline =  [
            $timelineKey  => [
                'action_by' => $this->deliveryman->first_name,
                'time'      => Carbon::now(),
                'details'   =>  $details 
            ]
        ];


        $updatedTimelineArray = array_merge($newTimeline,(array)$deliveryManOrder->time_line);

        $deliveryManOrder->note      = $request->input("note");
        $deliveryManOrder->status    = $request->input("status");
        $deliveryManOrder->time_line = $updatedTimelineArray;
        if($deliveryManOrder->assign_by && $deliveryManOrder->assign_by == $this->deliveryman->id){
            $deliveryManOrder->assign_by      = null;
            $deliveryManOrder->deliveryman_id = $this->deliveryman->id;
        }

        $deliveryManOrder->save();


        return api(
            [
                'message'  =>   $request->input("status") == DeliveryManOrder::ACCEPTED  
                                 ?  translate('Accepted successfully') :  translate('Declined successfully'),
            ])->success(__('response.success'));

    }



    /**
     * Summary of handleOrder
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleOrder(Request $request) : JsonResponse{


        $validator = Validator::make($request->all(),[
            'order_id'         => 'required|exists:orders,id',
            'status'           => 'required|in:'.DeliveryManOrder::DELIVERED.",".DeliveryManOrder::RETURN,
            'note'             => 'required'
        ]);


        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $order = Order::with(['customer'])
                    ->whereHas('deliveryManOrder' , fn(Builder $query) :Builder => 
                        $query->where(fn(Builder $query) : Builder => $query
                                    ->where('deliveryman_id',$this->deliveryman->id))
                                        ->where('status',DeliveryManOrder::ACCEPTED))
                                        ->where('id',$request->input('order_id'))
                                        ->whereNotIn('status',[Order::DELIVERED,Order::CANCEL,Order::RETURN])
                                        ->first();


        
       
        if(!$order) return api(['errors'=> [translate("Order not found")]])
                                           ->fails(__('response.fail'));



        if(site_settings('order_verification') == StatusEnum::true->status() &&  
            $request->input("status") == DeliveryManOrder::DELIVERED) {
             if($order->verification_code != $request->input('verification_code')) return api(['errors'=> [translate("Invalid order verification code")]])->fails(__('response.fail'));
        }

        $deliveryManOrder = $order->deliveryManOrder;

        $timelineKey = 'delivered';

        $details = translate('Order has been delivered by').$this->deliveryman->first_name;

        if( $request->input("status") == DeliveryManOrder::RETURN){
            $timelineKey = 'return';
            $details = translate('Order has been return from customer');
        }
        $newTimeline =  [
            $timelineKey  => [
                'action_by' => $this->deliveryman->first_name,
                'time'      => Carbon::now(),
                'details'   =>  $details 
            ]
        ];


        $updatedTimelineArray = array_merge($newTimeline,(array)$deliveryManOrder->time_line);

        $deliveryManOrder->note   = $request->input("note");
        $deliveryManOrder->status = $request->input("status");
        $deliveryManOrder->time_line = $updatedTimelineArray;

        $deliveryManOrder->save();

        return api(
            [
                'message'  =>   translate('Status updated')
            ])->success(__('response.success'));

    }



    #REAWRD POINT LOG 


    public function rewardPoint(Request $request) : JsonResponse{


        if(site_settings('deliveryman_club_point_system') == StatusEnum::false->status())  return api(['errors'=>  translate('Reward point system is currently inactive') ])->fails(__('response.fail'));


        $rewardPoints  = RewardPointLog::with(['order'])
                                    ->where('delivery_man_id',$this->deliveryman->id)
                                    ->latest()
                                    ->date()
                                    ->filter()
                                    ->paginate(site_settings('pagination_number',10))
                                    ->appends(request()->all());


            
    
        return api([
            'reward_logs'    => new RewardpointCollection($rewardPoints),
        ])->success(__('response.success'));


    }






    /**
     * Summary of getAssignedOrder
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssignedOrder() : JsonResponse{



        $assignedOrders = DeliveryManOrder::with(['assignBy','deliveryMan','order'])
                                            ->latest()
                                            ->date()
                                            ->filter()
                                            ->when(request()->input('type') == 'me' , fn(Builder $q):Builder  =>
                                                    $q->whereNotNull('deliveryman_id')
                                                            ->where('assign_by',$this->deliveryman->id)
                                                            ->where('deliveryman_id','!=',$this->deliveryman->id))
                                            ->when(request()->input('type') == 'others' , fn(Builder $q):Builder =>
                                                    $q->whereNotNull('assign_by')->where('deliveryman_id',$this->deliveryman->id))
                                            ->when(!request()->input('type') , fn(Builder $q):Builder => 
                                                    $q->whereNotNull('assign_by')
                                                            ->whereNotNull('deliveryman_id')
                                                            ->where(fn(Builder $q):Builder =>
                                                                    $q->where('deliveryman_id' ,$this->deliveryman->id)
                                                                            ->orWhere('assign_by',$this->deliveryman->id)))
                                            ->paginate(site_settings('pagination_number',10))
                                            
                                            ->appends(request()->all());


        return api([
            'assigned_orders'    => new DeliveryManOrderCollection($assignedOrders),
        ])->success(__('response.success'));



    }



    /**
     * Summary of redeemPoint
     * @return \Illuminate\Http\JsonResponse
     */
    public function redeemPoint(): JsonResponse{



        if(site_settings('deliveryman_club_point_system') == StatusEnum::false->status())  return api(['errors'=>  translate('Reward point system is currently inactive') ])->fails(__('response.fail'));


        $rewardAmountConfigurations  = !is_array(site_settings('deliveryman_reward_amount_configuration',[])) 
                                        ? json_decode(site_settings('deliveryman_reward_amount_configuration',[]),true) 
                                        : [];

        $point = (int) $this->deliveryman->point;

        

        $configuration =   collect($rewardAmountConfigurations)
                            ->filter(function ($item) use ($point) : bool {
                                $item = (object)($item);
                                return $point > $item->min_amount && $point <= $item->less_than_eq;
                            })->first();



        if(!@$configuration) return api(['errors'=> [translate("No reward available")]])
                            ->fails(__('response.fail'));

        $configuration = (object)  $configuration;
        

        if(!$configuration) return api(['errors'=> [translate("No reward available")]])
                                         ->fails(__('response.fail'));



        if((int)$configuration->amount < 1) return api(['errors'=> [translate("No reward available")]])
        ->fails(__('response.fail'));
        
        DB::transaction(function() use ($configuration  ) {

            $amount = (int)$configuration->amount;

            #CREATE POINT LOG

            $point = (int) $this->deliveryman->point;
            $details =  translate('Redeemed ').  $point . translate(' points and earned a ') . $configuration->name.translate(' Reward Bonus worth ').default_currency()->symbol.$amount;

            $pointLog                     = new RewardPointLog();
            $pointLog->delivery_man_id    = $this->deliveryman->id;
            $pointLog->post_point         = $this->deliveryman->point;
            $pointLog->point              = $this->deliveryman->point;
            $pointLog->details            = $details;
            $pointLog->save();
            
            $this->deliveryman->point=0;
            $this->deliveryman->save();

            #CREATE TRANSACTION
            $transaction = Transaction::create([
                'deliveryman_id'     => $this->deliveryman->id,
                'amount'             => $amount ,
                'post_balance'       => $this->deliveryman->balance,
                'transaction_type'   => Transaction::PLUS,
                'transaction_number' => trx_number(),
                'details' => $details,
            ]);

            $this->deliveryman->balance +=$amount;
            $this->deliveryman->save();

        });


        return api(
            [
                'message'  =>   translate('Redeemed successfully')
            ])->success(__('response.success'));

    }





    /**
     * Summary of getReferralLog
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReferralLog(): JsonResponse{


        if(site_settings('deliveryman_referral_system') != StatusEnum::true->status()) return api(['errors'=> [translate("This module is not available")]])
            ->fails(__('response.fail'));  



            $deliveryMen = DeliveryMan::with(['country','ratings'])
                                        ->latest()
                                        ->where('referral_id',$this->deliveryman->id)
                                        ->active()
                                        ->get();


            $totalReffered = $deliveryMen->count();

            $totalPointEarned =   $totalReffered*(int)site_settings('deliveryman_referral_reward_point',0);

            return api([ 

            'overview'              => [
                'total_point_earned' => $totalPointEarned,
                'total_reffered'     => $totalReffered,
            ],
            'delivery_men'          => new DeliveryManCollection($deliveryMen)
            ])->success(__('response.success'));


    }



}
