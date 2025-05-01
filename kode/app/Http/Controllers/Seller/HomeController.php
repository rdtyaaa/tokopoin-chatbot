<?php

namespace App\Http\Controllers\Seller;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SellerShopSetting;
use App\Models\WithdrawMethod;
use App\Models\Withdraw;
use App\Models\Transaction;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Order;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;
use App\Models\KycLog;
use App\Models\PlanSubscription;
use App\Models\SupportTicket;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Enums\KYCStatus;
class HomeController extends Controller
{
    public function dashboard()
    {
        $title   = translate('Seller dashboard');
        $seller  = Auth::guard('seller')->user();
        $data['physical'] = Product::sellerProduct()->physical()->where('seller_id', $seller->id)->count();
        $data['digital'] = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->count();
        $data['withdraw_amount'] = Withdraw::where('seller_id', $seller->id)->approved()->sum('amount');
        $data['total_ticket'] = SupportTicket::where("seller_id", $seller->id)->count();

        $data['total_subscription'] = PlanSubscription::where('seller_id', $seller->id)->count();


        $order['digital_order'] = Order::sellerOrder()->digitalOrder()->whereHas('orderDetails',function($q)use($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->count();

        $order['order'] = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails',function($q)use($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->count();

        $order['placed'] = Order::sellerOrder()->physicalOrder()->placed()->whereHas('orderDetails',function($q)use($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->count();

        $order['shipped'] = Order::sellerOrder()->physicalOrder()->shipped()->whereHas('orderDetails',function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->count();

        $order['canceled'] = Order::sellerOrder()->physicalOrder()->cancel()->whereHas('orderDetails',function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->count();

        $order['delivered'] = Order::sellerOrder()->physicalOrder()->delivered()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->count();

        $transactions = Transaction::whereNotNull('seller_id')->where('seller_id', $seller->id)->orderBy('id', 'DESC')->take(8)->get();

        $salesReport['month'] = collect([]);
        $salesReport['order_count'] = collect([]);

        $orderinfo = Order::sellerOrder()->physicalOrder()
            ->whereHas('orderDetails', function($q) use ($seller){
                $q->whereHas('product', function($query) use ($seller){
                    $query->where('seller_id', $seller->id);
                });
            })->selectRaw(DB::raw('count(*) as order_count'))
            ->selectRaw("DATE_FORMAT(created_at,'%M %Y') as months")
            ->groupBy('months')->get();

        $orderinfo->map(function ($query) use ($salesReport){
            $salesReport['month']->push($query->months);
            $salesReport['order_count']->push($query->order_count);
        });

        $monthlyOrderReport['monthly_order'] = collect([]);
        $orderReport = Order::sellerOrder()->physicalOrder()
            ->whereHas('orderDetails', function($q) use ($seller){
                $q->whereHas('product', function($query) use ($seller){
                    $query->where('seller_id', $seller->id);
                });
            })->whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('COUNT(CASE WHEN status = 1  THEN status END) AS placed')
            ->selectRaw('COUNT(CASE WHEN status = 2  THEN status END) AS confirmed')
            ->selectRaw('COUNT(CASE WHEN status = 3  THEN status END) AS processing')
            ->selectRaw('COUNT(CASE WHEN status = 4  THEN status END) AS shipped')
            ->selectRaw('COUNT(CASE WHEN status = 5  THEN status END) AS delivered')
            ->selectRaw('COUNT(CASE WHEN status = 6  THEN status END) AS cancel')->get()->toArray();


            $monthlyOrderReport = array_values($orderReport[0]);
        return view('seller.dashboard', compact('title','data','order','salesReport', 'monthlyOrderReport', 'transactions','seller'));
    }

    public function profile()
    {
        $title  = translate('Seller Profile');
        $seller = auth()->guard('seller')->user();
        return view('seller.profile', compact('title', 'seller'));
    }

    public function profileUpdate(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'phone'=>'max:15'
        ]);
        $seller = Auth::guard('seller')->user();
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        $address = [
            'address' => $request->address,
            'city'    => $request->city,
            'state'   => $request->state,
            'zip'     => $request->zip
        ];
        $seller->address = $address;
        if($request->hasFile('image')) {
            try {
                $removefile = $seller->image ?: null;
                $seller->image = store_file($request->image, file_path()['profile']['seller']['path'], file_path()['profile']['seller']['size'], $removefile);
            }catch (\Exception $exp){

            }
        }
        $seller->save();
        return redirect()->route('seller.profile')->with('success',translate("Your profile has been updated."));
    }

    public function password()
    {
        $title = translate('Password Update');
        return view('seller.password', compact('title'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request,[
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);
        $seller = Auth::guard('seller')->user();
        if (!Hash::check($request->current_password, $seller->password)) {
            return back()->with('error',translate("Password do not match !!"));

        }
        $seller->password = Hash::make($request->password);
        $seller->save();
        return redirect()->route('seller.profile')->with('success',translate("Password changed successfully"));
    }

    public function shopSetting()
    {
        $title       = translate('Manage shop setting');
        $shopSetting = SellerShopSetting::where('seller_id', Auth::guard('seller')->user()->id)->firstOrFail();
        return view('seller.shop.setting', compact('title', 'shopSetting'));
    }


    public function shopSettingUpdate(Request $request, $id)
    {


        $this->validate($request, [
            'name' => 'required|max:255|unique:seller_shop_settings,name,'.$id,
            'email' => 'nullable|email|unique:seller_shop_settings,email,'.$id,
            'phone' => 'required|unique:seller_shop_settings,phone,'.$id,
            'shop_logo' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'shop_first_image' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'seller_site_logo' => ['nullable','image',new FileExtentionCheckRule(file_format()),],
            'seller_site_logo_sm' => ['nullable','image',new FileExtentionCheckRule(file_format()),],
            'whatsapp_number' => ['required'],
            'whatsapp_order'   => ['required',Rule::in(array_values(StatusEnum::toArray()))],
        ]);
        $seller = Auth::guard('seller')->user();
        $shopSetting = SellerShopSetting::where('id', $id)->where('seller_id', $seller->id)->firstOrFail();
        $shopLogo = $shopSetting->shop_logo;
        $sellerSiteLogo = $shopSetting->seller_site_logo;
        $logoIcon       = $shopSetting->logoicon;
        $shop_first_image = $shopSetting->shop_first_image;

        if($request->hasFile('shop_logo')) {
            try {
                $shopLogo = store_file($request->shop_logo, file_path()['shop_logo']['path'], null, $shopLogo);
            }catch (\Exception $exp) {

            }
        }

        if($request->hasFile('seller_site_logo')) {
            try {
                $sellerSiteLogo = store_file($request->seller_site_logo, file_path()['seller_site_logo']['path'] , null,$sellerSiteLogo);
            }catch (\Exception $exp) {

            }
        }
        if($request->hasFile('seller_site_logo_sm')) {
            try {
                $logoIcon = store_file($request->seller_site_logo_sm, file_path()['seller_site_logo']['path'],null, $logoIcon);

            }catch (\Exception $exp) {

            }
        }

        if($request->hasFile('shop_first_image')) {
            try {
                $shop_first_image = store_file($request->shop_first_image, file_path()['shop_first_image']['path'], null, $shop_first_image);
            }catch (\Exception $exp) {

            }
        }



    $shopSetting->seller_id = Auth::guard('seller')->user()->id;
    $shopSetting->name = $request->name;
    $shopSetting->whatsapp_number = $request->whatsapp_number;
    $shopSetting->whatsapp_order  = $request->whatsapp_order;
    $shopSetting->email = $request->email;
    $shopSetting->phone = $request->phone;
    $shopSetting->address = $request->address;
    $shopSetting->short_details = $request->short_details;
    $shopSetting->shop_logo = $shopLogo;
    $shopSetting->seller_site_logo = $sellerSiteLogo;
    $shopSetting->logoicon = $logoIcon;
    $shopSetting->shop_first_image = $shop_first_image;
    $shopSetting->save();

    optimize_clear();
    return redirect()->back()->with('success',translate("Shop Setting Updated"));

    }

    public function withdrawMethod()
    {
        $title = translate('Withdraw method');
        $withdrawMethods = WithdrawMethod::where('status', 1)->get();
        return view('seller.withdraw.method', compact('title', 'withdrawMethods'));
    }

    public function withdrawMoney(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:withdraw_methods,id',
            'amount' => 'required|numeric|gt:0'
        ]);

        $withdrawMethod = WithdrawMethod::where('id', $request->id)->where('status', 1)->firstOrFail();
        $seller = Auth::guard('seller')->user();
        if($request->amount < $withdrawMethod->min_limit || $request->amount > $withdrawMethod->max_limit) {
            return back()->with('error',translate("Please follow withdraw limit"));
        }
        if ($request->amount > $seller->balance) {
            return back()->with('error',translate("You do not have sufficient balance for withdraw."));
        }
        $withdrawCharge = $withdrawMethod->fixed_charge + ($request->amount * $withdrawMethod->percent_charge / 100);
        $afterCharge = $request->amount - $withdrawCharge;
        $finalAmount = $afterCharge * $withdrawMethod->rate;

        $withdraw = new Withdraw();
        $withdraw->method_id = $withdrawMethod->id;
        $withdraw->seller_id = $seller->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency_id = $withdrawMethod->currency_id;
        $withdraw->rate = $withdrawMethod->rate;
        $withdraw->charge = $withdrawCharge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->trx_number = trx_number();
        $withdraw->created_at = Carbon::now();
        $withdraw->save();
        return redirect()->route('seller.withdraw.preview',encrypt($withdraw->trx_number));
    }

    public function withdrawPreview($trxNumber)
    {
        $title = translate('Withdraw Preview');
        $seller = Auth::guard('seller')->user();
        $withdraw = Withdraw::where('trx_number', decrypt($trxNumber))->where('status', 0)->where('seller_id', $seller->id)->firstOrFail();
        return view('seller.withdraw.preview', compact('title','withdraw'));
    }



    public function withdrawPreviewStore(Request $request, $id)
    {

        $seller = Auth::guard('seller')->user();
        $withdraw =  Withdraw::where('id', $id)->where('status', 0)->where('seller_id', $seller->id)->firstOrFail();
        if($withdraw->amount > $seller->balance) {
            return redirect()->back()->with('error',translate("Your request amount is larger then your current balance."));
        }
        $rules = [];
        if ($withdraw->method->user_information != null) {
            foreach ($withdraw->method->user_information as $key => $value) {
                $rules[$key] = ['required'];
                if($value->type == 'file'){
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if($value->type == 'text'){
                    array_push($rules[$key], 'max:191');
                }
                if($value->type == 'textarea'){
                    array_push($rules[$key], 'max:300');
                }
            }
        }
        $this->validate($request, $rules);
        $collection = collect($request);
        $userInformationData = [];
        if ($withdraw->method->user_information != null) {
            foreach ($collection as $firstKey => $firstValue) {
                foreach ($withdraw->method->user_information as $key => $value) {
                    if ($firstKey != $key){
                        continue;
                    }else{
                        if($value->type == 'file'){

                        }else{
                            $userInformationData[$key] = $firstValue;
                            $userInformationData[$key] = [
                                'data_name' => $firstValue,
                                'type' => $value->type,
                            ];
                        }
                    }
                }
            }
            $withdraw->withdraw_information = $userInformationData;
        }
        $withdraw->status = 2;
        $withdraw->save();

        $seller->balance  -=  $withdraw->amount;
        $seller->save();

        $transaction = Transaction::create([
            'seller_id' => $seller->id,
            'amount' => $withdraw->amount,
            'post_balance' => $seller->balance,
            'transaction_type' => Transaction::MINUS,
            'transaction_number' => $withdraw->trx_number,
            'details' => short_amount($withdraw->final_amount) .  ' Withdraw Via ' . $withdraw->method->name,
        ]);
        $mailCode = [
            'trx' => $withdraw->trx,
            'amount' => ($withdraw->amount),
            'charge' => ($withdraw->charge),
            'currency' => @session()->get('web_currency')->name,
            'rate' => ($withdraw->rate),
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency->name,
            'method_amount' => ($withdraw->final_amount),
            'user_balance' => ($seller->balance)
        ];


        SendMailJob::dispatch($seller,'WITHDRAW_REQUEST_AMOUNT',$mailCode);
        return redirect()->route('seller.withdraw.history')->with('success',translate("Withdraw request has been send"));


    }


    public function withdrawHistory()
    {
        $title = translate('Manage Withdraw log');
        $seller = Auth::guard('seller')->user();
        $withdraws = Withdraw::date()->search()->where('status', '!=', 0)->where('seller_id', $seller->id)->with('method', 'currency')->latest()->paginate(site_settings('pagination_number',10));
        return view('seller.withdraw.index', compact('title','withdraws'));
    }

    
    public function withdrawShow($id)
    {
        $title = translate('Withdraw Details');
        $seller = Auth::guard('seller')->user();
        $withdraw = Withdraw::date()->where('seller_id', $seller->id)
                     ->with('method', 'currency')->where('id',$id)->firstOrfail();
        return view('seller.withdraw.show', compact('title','withdraw','seller'));
    }



    public function transaction()
    {
        $title = translate('Transaction log');
        $seller = Auth::guard('seller')->user();
        $transactions = Transaction::search()->date()->whereNotNull('seller_id')->where('seller_id', $seller->id)->latest()->paginate(site_settings('pagination_number',10));
        return view('seller.transaction', compact('title', 'transactions'));
    }




   public function kycForm()  {

       $seller = Auth::guard('seller')->user(); 
       if($seller->kyc_status   == StatusEnum::true->status()) return  redirect()->route('seller.dashboard');     
       return view('seller.kyc_form',[
          'title'=> translate("KYC Application form")
       ]);

   }





   public function kycApplication(Request $request)  {

        $seller = Auth::guard('seller')->user();
        if($seller->kyc_status   == StatusEnum::true->status()) return  redirect()->route('seller.dashboard');

        $pendingKycs =  KycLog::where("seller_id",$seller->id)->pending()->count();

        if($pendingKycs > 0) return back()->with('error',translate('You already have a pending KYC request, Please wait for our confirmation'));


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


        $request->validate($rules, $message);



       $kycLog =   DB::transaction(function() use ($request ,$seller ) {


                      $customData = (Arr::except($request['kyc_data'],['files']));

                       $kycLog                  = new KycLog();
                       $kycLog->seller_id       = $seller->id;
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

  
       return redirect()->route("seller.kyc.log.list")->with(response_status('KYC application submitted! Verification in progress. We will notify you upon completion. Thank you for your patience'));
    
   }




    public function kycList() {

        $seller = Auth::guard('seller')->user(); 

        return view('seller.kyc_report',[
            'title'           => translate("KYC Reports"),
            "reports"         => KycLog::date()->where('seller_id',$seller->id)          
                                    ->latest()
                                    ->get()

        ]);

    }


    public function kycShow ($id) {

        $seller = Auth::guard('seller')->user(); 

        return view('seller.kyc_show',[
            'title'           => translate("KYC Details"),
            "report"         => KycLog::where('seller_id',$seller->id)
                                    ->where('id',$id)          
                                    ->latest()
                                    ->firstOrfail()

        ]);

    }

















}
