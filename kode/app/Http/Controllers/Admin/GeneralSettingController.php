<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Settings\CacheKey;
use App\Enums\Settings\GlobalConfig;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\WhatsAppMessage;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GeneralSettingController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:view_support'])->only('index');
        $this->middleware(['permissions:update_settings'])->only('socialLoginUpdate','updateSellerMode','updateDebugMode');
    }


    /**
     * Get setting view
     *
     * @return View
     */
    public function index() :View {



        $title   =  translate("System Setting");

        WhatsAppMessage::loadTemplatesFromWhatsApp();

        $wp_templates = Setting::where('key','wp_templates')->first();

        $templates  =  @collect(json_decode($wp_templates->value,true));

        $template   =  $templates->where('name',site_settings('wp_template'))->first();



        $variables = @$template['components'] ? $this->formatTemplateVariable(@$template['components']?? []) : NULL;



        $timeZones     = timezone_identifiers_list();
        return view('admin.setting.index', compact('title','timeZones','wp_templates','variables'));
    }


    /**
     * AI Configuration
     *
     * @return View
     */
    public function aiConfiguration() :View {

        $title   = translate('AI Configuration');
        return view('admin.ai_configuration', compact('title'));
    }

    /**
     * AI Configuration
     *
     * @return View
     */
    public function aiConfigurationUpdate(Request $request) :RedirectResponse {

        Setting::updateOrInsert(
            ['key'    => 'open_ai_setting'],
            ['value'  =>  json_encode($request->input('site_settings',[]))]
        );

        Cache::forget(CacheKey::SITE_SETTINGS->value);

        return redirect()->back()->with('success',translate('AI Configuration Updated'));

    }




    /**
     * Update general settings
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request) : RedirectResponse | array{




        $validations = $this->validationRules($request->site_settings);

        if($request->site_settings){
            $request->validate( $validations['rules'],$validations['message']);
            if(isset($request->site_settings['time_zone'])){
                $timeLocationFile = config_path('timesetup.php');
                $time = '<?php $timelog = '.$request->site_settings['time_zone'].' ?>';
                file_put_contents($timeLocationFile, $time);
            }


            if(($request->input('site_settings.seller_min_deposit_amount')) > 
               ($request->input('site_settings.seller_max_deposit_amount')) ||
               ($request->input('site_settings.customer_min_deposit_amount')) > 
               ($request->input('site_settings.customer_max_deposit_amount'))){
                return [
                    'status'  => false,
                    'message' => translate('Min deposit amount must be less that max deposit amount')
                ];
            }

            foreach(($request->input('site_settings')) as $key => $value){
                try {

                    if(in_array($key ,GlobalConfig::JSON_KEYS))   $value = json_encode($value);

                    Setting::updateOrInsert(
                        ['key'    => $key],
                        ['value'  => $value]
                    );
                    Cache::forget(CacheKey::SITE_SETTINGS->value);
                } catch (\Throwable $th) {

                }
            }
        }


        if($request->order_wise && is_array($request->order_wise)) return $this->setCustomerReawardPoint($request);

        if($request->delivery_order_wise && is_array($request->delivery_order_wise))    $this->setDeliveryManReawardPoint($request);


        if($request->delivery_reward_point && is_array($request->delivery_reward_point))  $this->setDeliveryManReaward($request);


        if(request()->ajax()){
            return [
                'status'  => true,
                'message' => translate('Setting has been updated')
            ];
        }

        return back()->with('success',translate('Setting has been updated'));
    }





    
    /**
     * Summary of setDeliveryManReaward
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function setDeliveryManReaward(Request $request) :array {


        $rewards = $request->input('delivery_reward_point');
        $rewardsAnount =  @$rewards['greater_than'];

        if($rewardsAnount){
            $price_configuration = [];

            foreach ($rewardsAnount as $index => $amount) {


                $minAmount  =  $amount;
                $maxAmount  =  @$rewards['less_than_eq'][$index] ?? 0;
                $amount     =  @$rewards['amount'][$index] ?? 0;
                $name       =  @$rewards['name'][$index] ??null;

                if(!$name){
                    return [
                        'status'  => false,
                        'message' => translate('point must be numeric value')
                    ];
                    
                }
  
                if(!is_numeric(  $minAmount ) || !is_numeric(  $maxAmount ) || !is_numeric($amount)) {
                    return [
                        'status'  => false,
                        'message' => translate('Price & point must be numeric value')
                    ];
                }

                 if($minAmount > $maxAmount){

                    return [
                        'status'  => false,
                        'message' => translate('Less than equal price must be higer than greater than prices')
                    ];

                 }
                 

                $price_configuration[] = [
                    "name"         => $name ,
                    "min_amount"   => $minAmount ,
                    "less_than_eq" => $maxAmount,
                    "amount"       => $amount
                ];
              
            }

            if(count($price_configuration) > 0 ) {

                Setting::updateOrInsert(
                    ['key'    => 'deliveryman_reward_amount_configuration'],
                    ['value'  => json_encode($price_configuration)]
                );
                Cache::forget(CacheKey::SITE_SETTINGS->value);
                return [
                    'status'  => true,
                    'message' => translate('Setting has been updated')
                ];
            }
        }

        return [
            'status'  => false,
            'message' => translate('Invalid configuration')
        ];



}




    /**
     * Summary of setDeliveryManReawardPoint
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function setDeliveryManReawardPoint(Request $request) :array {

            $orderWisePoint = $request->input('delivery_order_wise');
            $orderAmounts =  @$orderWisePoint['greater_than'];

            if($orderAmounts){
                $price_configuration = [];

                foreach ($orderAmounts as $index => $amount) {


                    $minAmount  =   $amount;
                    $maxAmount  =  @$orderWisePoint['less_than_eq'][$index] ?? 0;
                    $point      =  @$orderWisePoint['point'][$index] ?? 0;

                    if(!is_numeric(  $minAmount ) || !is_numeric(  $maxAmount ) ) {
                        return [
                            'status'  => false,
                            'message' => translate('Price must be numeric value')
                        ];
                    }

                     if($minAmount > $maxAmount){

                        return [
                            'status'  => false,
                            'message' => translate('Less than equal price must be higer than greater than prices')
                        ];

                     }
                     

                    $price_configuration[] = [
                        "min_amount"   => $minAmount ,
                        "less_than_eq" => $maxAmount,
                        "point" => $point
                    ];
                  
                }

                if(count($price_configuration) > 0 ) {

                    Setting::updateOrInsert(
                        ['key'    => 'deliveryman_reward_point_configuration'],
                        ['value'  => json_encode($price_configuration)]
                    );
                    Cache::forget(CacheKey::SITE_SETTINGS->value);
                    return [
                        'status'  => true,
                        'message' => translate('Setting has been updated')
                    ];
                }
            }

            return [
                'status'  => false,
                'message' => translate('Invalid configuration')
            ];



    }


    /**
     * Summary of setCustomerReawardPoint
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function setCustomerReawardPoint(Request $request) : array{

            $orderWisePoint = $request->input('order_wise');
            $orderAmounts =  @$orderWisePoint['greater_than'];

            if($orderAmounts){
                $price_configuration = [];

                foreach ($orderAmounts as $index => $amount) {


                    $minAmount  =   $amount;
                    $maxAmount  =  @$orderWisePoint['less_than_eq'][$index] ?? 0;
                    $point      =  @$orderWisePoint['point'][$index] ?? 0;

                    if(!is_numeric(  $minAmount ) || !is_numeric(  $maxAmount ) ) {
                        return [
                            'status'  => false,
                            'message' => translate('Price must be numeric value')
                        ];
                    }

                     if($minAmount > $maxAmount){
                        return [
                            'status'  => false,
                            'message' => translate('Less than equal price must be higer than greater than prices')
                        ];
                     }
                     

                    $price_configuration[] = [
                        "min_amount"   => $minAmount ,
                        "less_than_eq" => $maxAmount,
                        "point" => $point
                    ];
                  
                }

                if(count($price_configuration) > 0 ) {

                    Setting::updateOrInsert(
                        ['key'    => 'order_amount_based_reward_point'],
                        ['value'  => json_encode($price_configuration)]
                    );
                    Cache::forget(CacheKey::SITE_SETTINGS->value);
                    return [
                        'status'  => true,
                        'message' => translate('Setting has been updated')
                    ];
                }
            }

            return [
                'status'  => false,
                'message' => translate('Invalid configuration')
            ];


    }


     /**
     * settings validations
     * @return array
     */
    public function validationRules(array $request_data ,string $key = 'site_settings') :array{

        $rules      = [];
        $message    = [];

        $numreicKey = ["pagination_number",'max_login_attemtps','otp_expired_in','digit_after_decimal','search_max','search_min','minimum_order_amount','free_delivery_amount','refund_validity','max_otp_hit','otp_resend_time','temp_otp_block_time','max_login_attempt','status_expiry'];

        foreach(array_keys($request_data) as $data){

            if(in_array($data ,$numreicKey)){
                $rules[$key.".".$data] =  $data == "default_max_result" ? ['required','numeric','gt:-2','max:50000'] :['required','numeric','gt:-1','max:50000'];
            }
            else{
                $rules[$key.".".$data] = ['required'];
            }

            $message[$key.".".$data.'.required'] = ucfirst(str_replace('_',' ',$data)).' '.translate('Feild is Required');
        }


        return [

            'rules'   => $rules,
            'message' => $message
        ];
    }



    public function logoStore(Request $request) : RedirectResponse | array{


        $request->validate([
             'site_settings.site_logo'     => 'nullable|image|mimes:jpg,png,jpeg',
             'site_settings.admin_logo_lg' => 'nullable|image|mimes:jpg,png,jpeg',
             'site_settings.admin_logo_sm' => 'nullable|image|mimes:jpg,png,jpeg',
             'site_settings.site_favicon'  => 'nullable|image|mimes:jpg,png,jpeg',
             'invoice_logo.*'              => 'nullable|image|mimes:jpg,png,jpeg'
        ]);



        foreach(GlobalConfig::LOGO_KEYS as $key){

            try {
                $setting         = Setting::firstOrNew(['key' => $key]);
                if(isset($request['site_settings'][$key]) && is_file($request['site_settings'][$key]->getPathname())){
                    $setting->value = store_file($request['site_settings'][$key], file_path()['site_logo']['path'], null,  site_settings($key));
                    $setting->save();
                }

                if( $key ==  'invoice_logo' && $request->hasFile($key)){
                    $allInvoiceLogos             = json_decode(site_settings('invoice_logo'),true);
                    $invoiceLogos = $request->invoice_logo;
                    foreach($invoiceLogos as $key=>$value){
                        $allInvoiceLogos[$key] =  store_file($value, file_path()['invoiceLogo']['path'], null, $allInvoiceLogos[$key]);
                    }
                    $setting->value     = json_encode($allInvoiceLogos);
                    $setting->save();
                }

            } catch (\Exception $ex) {
                return [
                    'status'  => false,
                    'message' => $ex->getMessage()
                ];
            }
        }

        Cache::forget(CacheKey::SITE_SETTINGS->value);


        if(request()->ajax()){
            return [
                'status'  => true,
                'message' => translate('Logo settings has been updated')
            ];
        }

        return back()->with('success',translate('Logo setting has been updated'));








    }


    /**
     * Optimize clear
     *
     * @return RedirectResponse
     */
    public function cacheClear() :RedirectResponse
    {
        Artisan::call('optimize:clear');
        return back()->with('success',translate('Cache cleared successfully'));
    }


    /**
     * System overview
     *
     * @return View
     */
    public function systemInfo() :View
    {

        $systemInfo = [
            'laravel_version' => app()->version(),
            'server_detail'   => $_SERVER,
        ];
        return view('admin.system_info',[

            'title'           => "Server Information",
            'systemInfo'      =>  $systemInfo
        ]);
    }


    /**
     * Social Login settings
     *
     * @return View
     */
    public function socialLogin() :View
    {
        $title = translate('Social Login Credentials');
        return view('admin.setting.socal_login', compact('title'));
    }


    /**
     * Social login setting update
     *
     * @param Request $request
     * @return RedirectResponse | array
     */
    public function socialLoginUpdate(Request $request) :RedirectResponse | array
    {
        $this->validate($request, [
            'g_client_id'     => 'required',
            'g_client_secret' => 'required',
            'g_status'        => 'required|in:1,2',
            'f_client_id'     => 'required',
            'f_client_secret' => 'required',
            'f_status'        => 'required|in:1,2',
        ]);

        Setting::updateOrInsert(
            ['key'    => 's_login_google_info'],
            ['value'  => json_encode([
                'g_client_id'     => $request->g_client_id,
                'g_client_secret' => $request->g_client_secret,
                'g_status'        => $request->g_status,
            ])]
        );
        Setting::updateOrInsert(
            ['key'    => 's_login_facebook_info'],
            ['value'  => json_encode([
                'f_client_id'     => $request->f_client_id,
                'f_client_secret' => $request->f_client_secret,
                'f_status'        => $request->f_status,
            ])]
        );

        Cache::forget(CacheKey::SITE_SETTINGS->value);

        if(request()->ajax()){
            return [
                'status'  => true,
                'message' => translate('Social login setting has been updated')
            ];
        }


        return back()->with('success',translate('Social login setting has been updated'));
    }




    /**
     * Update selller mode
     *
     * @return string
     */
     public function updateSellerMode() :string {

        $general = GeneralSetting::first();
        $status  = 'active';
        if($general->seller_mode == 'active'){
            $status = 'inactive';
        }

        $general->seller_mode = $status;
        $general->save();
        return json_encode([
            'status'=> ucfirst($status)
        ]);
     }


     /**
      * Update debug mode
      *
      * @return string
      */
     public function updateDebugMode() :string {

        try {
            $path = base_path('.env');
            if (file_exists($path)) {
                if(env('APP_DEBUG')){
                    file_put_contents($path, str_replace(
                        "APP_DEBUG=true", "APP_DEBUG=false", file_get_contents($path)
                    ));
                }
                else{
                    file_put_contents($path, str_replace(
                        "APP_DEBUG=false", "APP_DEBUG=true", file_get_contents($path)
                    ));
                }
            }
        } catch (\Throwable $th) {

        }

        return json_encode([
            'status'=> true
        ]);
     }


     /**
      * Twak to setting
      *
      * @return View
      */
     public function plugin() :View{
        $title     = translate('Plugin Settings');
        return view('admin.setting.tawk',compact('title'));
     }


     /**
      * Twak to update
      *
      * @param Request $request
      * @return RedirectResponse | array
      */
     public function pluginUpdate(Request $request) :RedirectResponse | array {


        Setting::updateOrInsert(
            ['key'    => 'tawk_to'],
            ['value'  => json_encode($request->tawk,true)]
        );

        Cache::forget(CacheKey::SITE_SETTINGS->value);
        if(request()->ajax()){
            return [
                'status'  => true,
                'message' => translate('Setting has been updated')
            ];
        }
        return back()->with('success',translate('Plugin settings updated'));
     }



     /**
      * Flutter app setting Update
      *
      * @param Request $request
      * @return RedirectResponse
      */
     public function appSettingUpdate(Request $request) : RedirectResponse{


        $rules   = [
            "setting.*.heading"     => "required",
            "setting.*.description" => "required",
            "setting.*.image"       => ["required" , 'image',new FileExtentionCheckRule(file_format())],
        ];

        $app_settings = site_settings('app_settings',null) ? json_decode(site_settings('app_settings',null),true) : [];
        if(count($app_settings) > 0){
            $rules ['setting.*.image'] =  ['image',new FileExtentionCheckRule(file_format())];
        }

        $data = [];
        $request->validate($rules);
            if($request->setting){
             foreach($request->setting as $key=>$settings){
                $data[$key]['image'] = null;
                $removeImage         = null;

                if(isset($app_settings[$key]['image'])){
                    $data[$key]['image']  = $app_settings[$key]['image'];
                    $removeImage          = $app_settings[$key]['image'];
                }
                foreach($settings as $sub_key=>$setting){
                    if($sub_key == "image"){
                        $data[$key][$sub_key] = store_file($setting, file_path()['onboarding_image']['path'],null,$removeImage);
                    }
                    else{
                        $data[$key][$sub_key] = $setting;
                    }
                }

             }
            }




        Setting::updateOrInsert(
            ['key'    => 'app_settings'],
            ['value'  =>  json_encode($data)]
        );


        Cache::forget(CacheKey::SITE_SETTINGS->value);


        return back()->with('success',translate('Flutter APP onboarding page settings updated'));

     }




     /**
      * Flutter app on boarding settings
      *
      * @return View
      */
     public function appSettings() :View{

        $title = "Flutter App Settings";
        return view('admin.setting.app_setting',compact('title'));
     }













      /**
      * Deliveryman app setting Update
      *
      * @param Request $request
      * @return RedirectResponse
      */
      public function deliverymanAppappSettingUpdate(Request $request) : RedirectResponse{


        $rules   = [
            "setting.*.heading"     => "required",
            "setting.*.description" => "required",
            "setting.*.image"       => ["required" , 'image',new FileExtentionCheckRule(file_format())],
        ];

        $app_settings = site_settings('app_settings',null) ? json_decode(site_settings('app_settings',null),true) : [];
        if(count($app_settings) > 0){
            $rules ['setting.*.image'] =  ['image',new FileExtentionCheckRule(file_format())];
        }

        $data = [];
        $request->validate($rules);
            if($request->setting){
             foreach($request->setting as $key=>$settings){
                $data[$key]['image'] = null;
                $removeImage         = null;

                if(isset($app_settings[$key]['image'])){
                    $data[$key]['image']  = $app_settings[$key]['image'];
                    $removeImage          = $app_settings[$key]['image'];
                }
                foreach($settings as $sub_key=>$setting){
                    if($sub_key == "image"){
                        $data[$key][$sub_key] = store_file($setting, file_path()['onboarding_image']['path'],null,$removeImage);
                    }
                    else{
                        $data[$key][$sub_key] = $setting;
                    }
                }

             }
            }




        Setting::updateOrInsert(
            ['key'    => 'deliveryman_app_settings'],
            ['value'  =>  json_encode($data)]
        );


        Cache::forget(CacheKey::SITE_SETTINGS->value);


        return back()->with('success',translate('Flutter APP onboarding page settings updated'));

     }




     /**
      * Deliveryman app on boarding settings
      *
      * @return View
      */
     public function deliverymanAppSettings() :View{

        $title = translate("Delivery Man APP Settings");
        return view('admin.setting.delivery_app_setting',compact('title'));
     }




     /**
      * Load whatsapp templates
      *
      * @return array
      */
    public function loadTemplates() :array {



        $response = WhatsAppMessage::loadTemplatesFromWhatsApp();

        if(!Arr::get( $response , 'status')){
            return   $response ;
        }

        $wp_templates = Setting::where('key','wp_templates')->first();

        if($wp_templates){
            return [
                'status'    => true ,
                'templates' => json_decode($wp_templates->value,true) ,
            ];
        }

        return [
            'status'    => false ,
            'message'   => translate('No templates found in your whatsapp business account') ,
        ];


    }




     /**
      * Get whatsapp templates
      *
      * @return array
      */
      public function getTemplate(Request $request) :array {

        $request->validate([
            'template_name' => "required",
        ],[
            'template_name.required' => translate("Please select a template first")
        ]);


        $wp_templates = Setting::where('key','wp_templates')->first();

        if(!$wp_templates){
            return [
                'status'    => true ,
                'message'   => translate("No templates found"),
            ];
        }

        $templates  =  collect( json_decode($wp_templates->value,true));

        $template   =  $templates->where('name',$request->input('template_name'))->first();

        if(!$template){
            return [
                'status'    => true ,
                'message'   => translate("No templates found"),
            ];
        }


        $variables = $this->formatTemplateVariable($template['components']);

        $loaded =  true ;
        return [
            'status'      => true ,
            "preview_html" => view('admin.setting.partials.wp_preview_section', compact('template','variables','loaded'))->render()
        ];



     }

     public function formatTemplateVariable(array $components): array {

        $variables = [];

        foreach ( $components as $item) {

            if($item['type']=="HEADER"&&$item['format']=="TEXT"){
                preg_match_all('/{{(\d+)}}/', $item['text'], $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $id) {
                        $exampleValue ="";
                        try {
                            $exampleValue = $item['example']['header_text'][$id - 1];
                        } catch (\Throwable $th) {
                        }
                        $variables['header'][] = ['id' => $id, 'exampleValue' => $exampleValue];
                    }
                }
            }else if($item['type']=="HEADER"&&$item['format']=="DOCUMENT"){
                $variables['document']=true;
            }else if($item['type']=="HEADER"&&$item['format']=="IMAGE"){
                $variables['image']=true;
            }else if($item['type']=="HEADER"&&$item['format']=="VIDEO"){
                $variables['video']=true;
            }else if($item['type']=="BODY"){
                preg_match_all('/{{(\d+)}}/', $item['text'], $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $id) {
                        $exampleValue ="";
                        try {
                            $exampleValue = $item['example']['body_text'][0][$id - 1];
                        } catch (\Throwable $th) {
                        }
                        $variables['body'][] = ['id' => $id, 'exampleValue' => $exampleValue];
                    }
                }
            }else if($item['type']=="BUTTONS"){
                foreach ($item['buttons'] as $keyBtn => $button) {
                    if($button['type']=="URL"){
                        preg_match_all('/{{(\d+)}}/', $button['url'], $matches);

                        if (!empty($matches[1])) {

                            foreach ($matches[1] as $id) {
                                $exampleValue ="";
                                try {
                                    $exampleValue = $button['url'];
                                    $exampleValue = str_replace("{{1}}", "", $exampleValue );
                                } catch (\Throwable $th) {
                                }
                                $variables['buttons'][$id - 1][] = ['id' => $id, 'exampleValue' => $exampleValue,'type'=>$button['type'],'text'=>$button['text']];
                            }
                        }
                    }
                    if($button['type']=="COPY_CODE"){
                        $exampleValue = $button['example'][0];
                        $variables['buttons'][$keyBtn][] = ['id' => $keyBtn, 'exampleValue' => $exampleValue,'type'=>$button['type'],'text'=>$button['text']];
                    }

                }

            }
        }

        return $variables ;
     }




     public function storeTemplate(Request $request){


        $request->validate([
            'site_settings.wp_template' => 'required'
        ]);


        $templteName  = $request->site_settings['wp_template'];


        $settings = [
            [
                'key' => 'wp_template',
                'value' => $templteName
            ],
            [
                'key' => 'wp_paramvalues',
                'value' => $request->input('paramvalues') ? json_encode($request->input('paramvalues')) : null
            ],
            [
                'key' => 'wp_parammatch',
                'value' => $request->input('parammatch') ? json_encode($request->input('parammatch')) : null
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrInsert(['key' => $setting['key']], $setting);
        }

        Cache::forget(CacheKey::SITE_SETTINGS->value);

        $variablesValues= json_decode(site_settings('wp_paramvalues'),true);
        $variables_match= json_decode(site_settings('wp_parammatch'),true);


        $wp_templates = Setting::where('key','wp_templates')->first();
        $templates  =  collect( json_decode($wp_templates->value,true));

        $template   =  $templates->where('name',site_settings('wp_template'))->first();



        $content="";
        $header_text="";

        $footer="";
        $buttons=[];


       //Make the components
       $components = ($template['components']);
       $APIComponents=[];
       foreach ($components as $keyComponent => $component) {
            $lowKey=strtolower($component['type']);


            if($component['type']=="HEADER"&&$component['format']=="TEXT"){
                $header_text=$component['text'];
                $component['parameters']=[];


                if(isset($variables_match[$lowKey])){
                    $this->setParameter($variables_match[$lowKey],($variablesValues[$lowKey]),$component,$header_text);
                    unset($component['text']);
                    unset($component['format']);
                    unset($component['example']);
                    array_push($APIComponents,$component);
                }

            }else if($component['type']=="BODY"){
                $content=$component['text'];
                $component['parameters']=[];
                if(isset($variables_match[$lowKey])){
                    $this->setParameter($variables_match[$lowKey],($variablesValues[$lowKey]),$component,$content);

                    unset($component['text']);
                    unset($component['format']);
                    unset($component['example']);
                    array_push($APIComponents,$component);
                }

            }else if($component['type']=="FOOTER"){
                $footer=$component['text'];
            }else if( $component['type']=="BUTTONS"){
                $keyButton=0;
                foreach ($component['buttons'] as $keyButtonFromLoop => $valueButton) {

                    if(isset($variables_match[$lowKey][$keyButton]) && (($valueButton['type']=="URL"&&stripos($valueButton['url'], "{{") !== false) || ($valueButton['type']=="COPY_CODE")) ){
                        $buttonName="";
                        $button=[
                            "type"=>"button",
                            "sub_type"=>strtolower($valueButton['type']),
                            "index"=>$keyButtonFromLoop."",
                            "parameters"=>[]
                        ];
                        $paramType="text";
                        if($valueButton['type']=="COPY_CODE"){
                            $paramType="coupon_code";
                        }

                        $this->setParameter($variables_match[$lowKey][$keyButton],$variablesValues[$lowKey][$keyButton],$button,$buttonName,$paramType);


                        array_push($APIComponents,$button);
                        array_push($buttons,$valueButton);
                        $keyButton++;
                    }else{
                        array_push($buttons,$valueButton);
                    }

                }

            }


      }
       $components=$APIComponents;




     $settings = [
            [
                'key' => 'wp_notification_message_component',
                'value' => json_encode($components)
            ],
            [
                'key' => 'wp_footer_text',
                'value' =>  $footer
            ],
            [
                'key' => 'wp_header_text',
                'value' => $header_text
            ],
            [
                'key' => 'wp_full_message',
                'value' => $content
            ],
            [
                'key' => 'wp_buttons',
                'value' => json_encode($buttons)
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrInsert(['key' => $setting['key']], $setting);
        }

        Cache::forget(CacheKey::SITE_SETTINGS->value);

        $variables = $this->formatTemplateVariable($template['components']);

        return [
            'status'  => true,
            'message' => translate('Setting has been updated'),
            "preview_html" => view('admin.setting.partials.wp_preview_section', compact('template','variables'))->render()

        ];
     }

     private function setParameter($variables,$values,&$component,&$content,$type="text"){

        foreach ($variables as $keyVM => $vm) {

            $data=["type"=>$type];

            $data[$type]= $vm ? $vm : @$values[$keyVM];
            array_push($component['parameters'],$data);

            $content=str_replace("{{".$keyVM."}}",$data[$type],$content);

        }
    }









    
    /**
     * get kyc settings
     * 
     * @return View
     */
    public function kycConfig() :View
    {
        return view('admin.setting.seller_kyc_settings',[
            'title'       => 'KYC Configuration',
        ]);
    }


    /**
     * KYC settings
     *
     * @param Request $request
     * @return string
     */
    public function kycSetting(Request $request) :array {


        $request->validate([
            'custom_inputs.*.labels'      => ['required'],
            'custom_inputs.*.type'        => ['required',Rule::in(['text','file','textarea','date','email','number'])],
            'custom_inputs.*.required'    => ['required',Rule::in(StatusEnum::toArray())],
            'custom_inputs.*.placeholder' => ['required'],
            'custom_inputs.*.default'     => ['required'],
            'custom_inputs.*.multiple'    => ['required'],
        ]);

        $status             =  false;
        $promptInputs       = [];
        foreach ($request->input('custom_inputs',[]) as $index => $field) {
            $newField = $field;
            if (is_null($field['name'])) {
                $newField['name'] = t2k($newField['labels']);
            }
            $promptInputs[$index] = $newField;
        }

        $request->merge(['custom_inputs' => $promptInputs]);

        try {
            $status   =  true;
            $message  =  translate("Setting has been updated");
         
            Setting::updateOrInsert(
                ['key'   =>  'seller_kyc_settings'],
                ['value' =>  json_encode($promptInputs)]
            );
    
          } catch (\Exception $exception) {
     
            $message = $exception->getMessage();
         }



        Cache::forget(CacheKey::SITE_SETTINGS->value);
        if(request()->ajax()){
            return [
                'status'  => $status ,
                'message' => $message
            ];
        }

    }


    











}
