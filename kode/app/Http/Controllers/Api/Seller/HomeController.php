<?php

namespace App\Http\Controllers\Api\Seller;

use App\Enums\KYCStatus;
use App\Http\Resources\Seller\KycLogResource;
use Illuminate\Support\Facades\DB;
use App\Enums\Status;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\ShopSettingRequest;
use App\Http\Resources\CurrencyCollection;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\LanguageCollection;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PaymentLogCollection;
use App\Http\Resources\PaymentLogResource;
use App\Http\Resources\PaymentMethodCollection;
use App\Http\Resources\Seller\AttributeCollection;
use App\Http\Resources\Seller\BrandCollection;
use App\Http\Resources\Seller\CampaignCollection;
use App\Http\Resources\Seller\CategoryCollection;
use App\Http\Resources\Seller\ConfigResource;
use App\Http\Resources\Seller\KycLogCollection;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Resources\Seller\ShippingDelevaryCollection;
use App\Http\Resources\Seller\SubscriptionResource;
use App\Http\Resources\Seller\TransactionCollection;
use App\Http\Resources\TaxConfigurationCollection;
use App\Http\Services\Seller\SellerService;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Currency;
use App\Models\KycLog;
use App\Models\Language;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\PlanSubscription;
use App\Models\Seller;
use App\Models\ShippingDelivery;
use App\Models\Tax;
use App\Rules\General\FileExtentionCheckRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    protected ? Seller $seller;

    public function __construct(protected SellerService $sellerService){
        $this->middleware(function ($request, $next) {
            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);
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
        $paymentMethods         = PaymentMethod::with(['currency'])->active()->get();


        return api([ 
            'config'                  => new ConfigResource(general_setting()),
            'languages'               => new LanguageCollection($languages),
            'default_language'        => new LanguageResource($defaultLanguage),
            'currency'                => new CurrencyCollection($currencies),
            'default_currency'        => new CurrencyResource(default_currency()),
            'payment_methods'         => new PaymentMethodCollection($paymentMethods),
            'image_format'               => file_format(),
            'file_format'                => file_format('file'),

        ])->success(__('response.success'));

    }




    /**
     * Get dashboard overview
     *
     * @return JsonResponse
     */
    public function dashboard() : JsonResponse {


        $now = Carbon::now()->toDateTimeString();
        
        $campaigns          =  Campaign::whereHas('products',fn (Builder $q) : Builder =>  $q->where('seller_id', $this->seller->id))
                                    ->with([
                                    'products' => fn (BelongsToMany $q) : BelongsToMany =>  $q->where('seller_id', $this->seller->id) ,'products.category', 'products.brand', 'products.subCategory', 'products.order','products.gallery','products.stock','products.review','products.review.customer','products.shippingDelivery','products.digitalProductAttribute','products.digitalProductAttribute. digitalProductAttributeValueKey'])
                                    ->where('status',Status::ACTIVE)
                                    ->where('start_time',"<=",$now)
                                    ->where('end_time',">=",$now)
                                    ->take(4)
                                    ->get();
    
        

        return api([ 
            'seller'                  => new SellerResource($this->seller),
            'overview'                => $this->sellerService->getDashboardOverview($this->seller),
            'graph_data'              => $this->sellerService->getGraphData($this->seller),
            'transaction'             => new TransactionCollection($this->sellerService->getLatestTransaction($this->seller)),
            'campaigns'               => new CampaignCollection( $campaigns ),
        ])->success(__('response.success'));
    }



    /**
     * Get all campaigns
     *
     * @return JsonResponse
     */
    public function campaigns() : JsonResponse {


        $now = Carbon::now()->toDateTimeString();
        
        $campaigns          =  Campaign::whereHas('products',fn (Builder $q) : Builder =>  $q->where('seller_id', $this->seller->id))
                                    ->with([
                                    'products' => fn (BelongsToMany $q) : BelongsToMany =>  $q->where('seller_id', $this->seller->id) ,'products.category', 'products.brand', 'products.subCategory', 'products.order','products.gallery','products.stock','products.review','products.review.customer','products.shippingDelivery','products.digitalProductAttribute','products.digitalProductAttribute. digitalProductAttributeValueKey'])
                                    ->where('status',Status::ACTIVE)
                                    ->where('start_time',"<=",$now)
                                    ->where('end_time',">=",$now)
                                    ->paginate(paginate_number());
         
    
        

        return api([ 
            'campaigns'               => new CampaignCollection( $campaigns ),
        ])->success(__('response.success'));
    }



    
    /**
     * Get seller shop
     *
     * @return JsonResponse
     */
    public function shop() : JsonResponse{
        

        return api([ 
            'seller'                  => new SellerResource($this->seller),
        ])->success(__('response.success'));
    }



    /**
     * Get depositLog
     *
     * @return JsonResponse
     */
    public function depositLog() : JsonResponse{
        
        $logs = PaymentLog::with(['paymentGateway','paymentGateway.currency'])->filter()->date()->whereNotNull('seller_id')->where('seller_id',$this->seller->id)
                                            ->paginate(site_settings('pagination_number',10))
                                            ->appends(request()->all());

        return api([ 
            'deposit_logs' => new PaymentLogCollection($logs),
        ])->success(__('response.success'));

    }



    /**
     * Summary of makeDeposit
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeDeposit(Request $request) : JsonResponse{

        $validator = Validator::make($request->all(),[
            'amount' => 'required|numeric|gt:0',
            'payment_id' => 'required|exists:payment_methods,id',
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        
        $method = PaymentMethod::with('currency')
                                ->active()
                                ->where('id',$request->input('payment_id'))
                                ->first();
            
        if(!$method)  return api(['errors'=> translate('Invalid payment method') ])->fails(__('response.fail'));
        
        $amount = ((int) $request->input('amount'));


        if(  $amount  < (int) site_settings('seller_min_deposit_amount',0) ||  
        $amount  > (int) site_settings('seller_max_deposit_amount',0)) return api(['errors'=> translate('Please follow the deposit limit') ])->fails(__('response.fail'));

        $log = WalletRecharge::creteLog($this->seller , $method , $amount);

        if($method->type == PaymentMethod::MANUAL){
            $log->custom_info =  $request->input("custom_input");
            $log->save();
            return api(
                [
                    'message'      => translate('Your request is submitted, please wait for confirmation'),
                    'log'        => new PaymentLogResource($log),
                ])->success(__('response.success'));
        }

        $response = ['payment_log'  => new PaymentLogResource($log)];
        $paymentUrl = $this->getPaymentURL($method ,$log);
        if($paymentUrl) $response['payment_url'] = $paymentUrl;
        return api($response)->success(__('response.success'));


    }



    




    /**
     * Update shop settings
     *
     * @param ShopSettingRequest $request
     * @return JsonResponse
     */
    public function shopUpdate(ShopSettingRequest $request) : JsonResponse{


        $response  = $this->sellerService->updateSellerShop($request ,$this->seller);

        return api(
            [
                'message' => translate('Seller shop setting updated'),
                'seller'  => new SellerResource($this->seller),
            ])->success(__('response.success'));

    }




    /**
     * Get seller transaction
     *
     * @return JsonResponse
     */
    public function transactions() : JsonResponse {

        return api([ 
            'transaction'   => new TransactionCollection($this->sellerService->getPaginatedTransaction($this->seller))
        ])->success(__('response.success'));

    }





    /**
     * Get seller auth config 
     *
     * @return JsonResponse
     */
    public function authConfig() : JsonResponse {
        
        $subscription = PlanSubscription::where('seller_id',$this->seller ->id)
                                    ->where('status',PlanSubscription::RUNNING)
                                    ->where('expired_date','>' ,Carbon::now()->toDateTimeString())
                                    ->first();


        $categories   = Category::with(['children'])
                                    ->active()
                                    ->parentCategory()
                                    ->get();


        $brands       = Brand::active()->get();



        $attribues    = Attribute::with('value')
                                    ->active()
                                    ->get();


        $shippingDeliveries = ShippingDelivery::active()->get();


        $taxes =  Tax::active()->get();


        return api([ 

            'product_size_guide'         => [
                  'featured_image'=> file_path()['product']['featured']['size'],
                  'gallery_image' => file_path()['product']['gallery']['size']
            ],
            'attribute_value_file_extensions' => ['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],
            'has_subscription_running'   => $subscription ? true : false,
            'subscription'               => $subscription ? new SubscriptionResource( $subscription) : null,
            'categories'                 => new CategoryCollection( $categories ),
            'brands'                     => new BrandCollection( $brands  ),
            'attributes'                 => new AttributeCollection( $attribues  ),
            'shipping_deliveries'        => new ShippingDelevaryCollection($shippingDeliveries),
            'tax_configuration'          => new TaxConfigurationCollection($taxes)


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

        $this->seller->fcm_token = $request->input('fcm_token');
        $this->seller->save();

        return api(
            [
                'message'   => translate("Token updated")
            ])->success(__('response.success'));

    }


    /**
     * KYC application
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function  kycLog() :JsonResponse{
        $logs =  KycLog::where("seller_id",$this->seller->id)->get();
        return api([ 
            'kyc_logs'          => new KycLogCollection($logs)

        ])->success(__('response.success'));


    }






    /**
     * KYC application
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function  kycApplication(Request $request) :JsonResponse{


        $pendingKycs =  KycLog::where("seller_id",$this->seller->id)->pending()->count();

        if($pendingKycs > 0) return api(['errors'=>'You already have a pending KYC request, Please wait for our confirmation'])->fails(__('response.fail'));


        $rules = [];
        $message = [];

        $kycSettings     = !is_array(site_settings('seller_kyc_settings',[])) 
                            ?  json_decode(site_settings('seller_kyc_settings',[]),true) 
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
                        $kycLog->seller_id       = $this->seller->id;
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




    





}
