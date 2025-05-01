<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CustomerDeliverymanConversation;
use App\Models\DeliveryMan;
use App\Models\DeliverymanEarningLog;
use App\Models\DeliveryManRating;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Enums\Settings\CacheKey;
use App\Enums\Settings\GlobalConfig;
use App\Enums\StatusEnum;
use App\Models\RewardPointLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class DeliveryManController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:manage_delivery_man']);
    }

    public function list()
    {
        $title           = translate("Manage Delivery-man");
        $deliverymanlist = DeliveryMan::with(['orders','ratings'])->search()->latest()->paginate(site_settings('pagination_number',10));
        return view('admin.delivery_man.list', compact('title','deliverymanlist'));
    }


    public function create()
    {
        $title     = translate("Create Delivery-Man");
        $countries  = Country::get();
        return view('admin.delivery_man.create', compact('title','countries'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'first_name'   => "required|max:191",
            'last_name'    => "nullable|string",
            'username'     => "required|unique:delivery_men,username",
            'email'        => "required|unique:delivery_men,email",
            'phone'        => "required|unique:delivery_men,phone",
            'password'     => "required",
            'phone_code'   => "required",
            'country_id'   => "required|exists:countries,id",
            'latitude'     => "required",
            'longitude'    => "required",
            'address'      => "required",
            'image'        => [ new FileExtentionCheckRule(file_format())],
        ]);

        $deliveryman = new DeliveryMan();

        $deliveryman->first_name = $request->first_name;
        $deliveryman->last_name = $request->last_name;
        $deliveryman->email  = $request->email;
        $deliveryman->username = $request->username;
        $deliveryman->phone = $request->phone;
        $deliveryman->password = Hash::make($request->password);
        $deliveryman->phone_code = $request->phone_code;
        $deliveryman->country_id = $request->country_id;
        $deliveryman->address = [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'address'   => $request->address,
        ];

        if($request->hasFile('image')){
            try{
                $deliveryman->image = store_file($request->image, file_path()['profile']['delivery_man']['path']);
            }catch (\Exception $exp){

            }
        }


        $kyc =  [];
        if($request->input("key_name")){

            $keys   = $request->input("key_name");
            $values = $request->input("value");
            $files  = $request->input("file");

            for( $i = 0 ; $i<count($keys); $i++){

                $key = t2k($keys[$i]);
                $data =  [];
                $data['key']   =    $key;
                $data['value'] = $values[$i];
                $file  = null;
                if(isset($files[$i])){
                    try{
                        $file = store_file($files[$i], file_path()['delivery_man_kyc']['path']);
                    }catch (\Exception $exp){

                    }
                }

                $data['file'] = $file;
                $kyc[$key] = $data;
            }
        }

        $deliveryman->kyc_data =     $kyc ;

        $deliveryman->save();


        return back()->with('success',translate('Created successfully'));



    }



    public function overview($id)
    {

        $title     = translate("Delivery-man Statistics");

        $deliveryman = DeliveryMan::withCount(['orders'])->findOrfail($id);

        $rating = round(DeliveryManRating::with(['user'])
                        ->latest()
                        ->where('delivery_men_id',$id)
                        ->avg('rating'),site_settings('digit_after_decimal',2));


        $overview = [
            'withdraw' => [
                "log" => Withdraw::with(['currency','method'])
                                    ->whereNull('seller_id')
                                    ->where('deliveryman_id',$id)
                                    ->date()
                                    ->search()
                                    ->where('status', '!=', 0)
                                    ->latest()
                                    ->take(10)
                                    ->get(),

                "total_success_withdraw"   => round(Withdraw::where('status', PaymentLog::SUCCESS)
                                                            ->whereNull('seller_id')
                                                            ->where('deliveryman_id',$id)->sum('amount')),


                "total_pending_withdraw"   => round(Withdraw::pending()
                                                        ->whereNull('seller_id')
                                                        ->where('deliveryman_id',$id)->sum('amount')),


                "total_rejected_withdraw"   => round(Withdraw::rejected()
                                                        ->whereNull('seller_id')
                                                        ->where('deliveryman_id',$id)->sum('amount'))
            ],


            "earning_log" => DeliverymanEarningLog::latest()
                                       ->where('deliveryman_id',$id)
                                       ->with(['order','order.orderDetails','order.orderDetails.product'])
                                       ->latest()
                                       ->take(5)
                                       ->get(),

            "transaction_log" => Transaction::latest()
                                        ->where('deliveryman_id',$id)
                                       ->deliverymen()
                                       ->search()
                                       ->date()
                                       ->latest()
                                       ->with('deliveryman')
                                       ->latest()
                                       ->take(5)
                                       ->get(),




            "customer_reviews" => DeliveryManRating::with(['user','order','order.orderDetails','order.orderDetails.product'])
                                                ->latest()
                                                ->where('delivery_men_id',$id)
                                                ->paginate(site_settings('pagination_number',10)),


        ];

        return view('admin.delivery_man.overview', compact('title','deliveryman','overview','rating'));
    }


    public function earning($id)
    {
        $title     = translate("Delivery-man earnings");

        $deliveryman = DeliveryMan::withCount(['orders'])->findOrfail($id);
        $earningLogs = DeliverymanEarningLog::with(['order','order.orderDetails','order.orderDetails.product'])
                                                ->where('deliveryman_id', $deliveryman->id)
                                                ->latest()
                                                ->paginate(site_settings('pagination_number',10));

        return view('admin.delivery_man.overview', compact('title','deliveryman','earningLogs'));
    }

    public function cashCollect(Request $request){

        $request->validate([
            'id'           => 'required|exists:delivery_men,id',
            'amount'       => 'required|numeric|gt:0',
        ]);

        $deliveryman = DeliveryMan::findOrfail(request()->input('id'));

        if($request->amount > $deliveryman->order_balance) return back()->with('error',translate('Deliveryman Doesnot have enough order balance'));


        $deliveryman->order_balance -= $request->amount;
        $deliveryman->save();
        $transaction = Transaction::create([
            'deliveryman_id'     => $deliveryman->id,
            'amount'             => $request->amount,
            'post_balance'       => $deliveryman->order_balance,
            'transaction_type'   => Transaction::MINUS,
            'transaction_number' => trx_number(),
            'details'            => show_amount($request->amount ,default_currency()->symbol).' Cash out by admin',
        ]);

        return back()->with('success',translate('Cash out completed'));

    }

    public function edit($id)
    {
        $title     = "Update Delivery-Man";
        $countries  = Country::get();

        $deliveryman = DeliveryMan::findOrfail($id);
        return view('admin.delivery_man.edit', compact('title','countries','deliveryman'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'id'           => 'required|exists:delivery_men,id',
            'first_name'   => "required|max:191",
            'last_name'    => "nullable|string",
            'username'     => "required|unique:delivery_men,username,".request()->input('id'),
            'email'        => "required|unique:delivery_men,email,".request()->input('id'),
            'phone'        => "required|unique:delivery_men,phone,".request()->input('id'),
            'phone_code' => "required",
            'country_id'   => "required|exists:countries,id",
            'latitude'     => "required",
            'longitude'    => "required",
            'address'      => "required",
            'image'        => [ new FileExtentionCheckRule(file_format())],

        ]);

        $deliveryman = DeliveryMan::findOrfail(request()->input('id'));

        $deliveryman->first_name = $request->first_name;
        $deliveryman->last_name = $request->last_name;
        $deliveryman->email  = $request->email;
        $deliveryman->username = $request->username;
        $deliveryman->phone = $request->phone;
        $deliveryman->phone_code = $request->phone_code;
        $deliveryman->country_id = $request->country_id;
        $deliveryman->address = [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'address'   => $request->address,
        ];

        if($request->hasFile('image')){
            try{
                $deliveryman->image = store_file($request->image, file_path()['profile']['delivery_man']['path'] ,null ,  $deliveryman->image);
            }catch (\Exception $exp){

            }
        }

        $kyc =  [];
        if($request->input("key_name")){

            $keys   = $request->input("key_name");
            $oldKeys  = $request->input("old_key");
            $values = $request->input("value");
            $files  = $request->input("file");

            for( $i = 0 ; $i<count($keys); $i++){
                $key   = t2k($keys[$i]);

                $file   = null;

                if(isset($oldKeys[$i])){
                    $oldKey = t2k($oldKeys[$i]);
                    $file  = @$deliveryman->kyc_data->{$oldKey}->file ?? null;
                }


                $data =  [];
                $data['key']   = $key;
                $data['value'] = $values[$i];

                if(isset($files[$i])){
                    try{
                        $file = store_file($files[$i], file_path()['delivery_man_kyc']['path'],null ,        $file );
                    }catch (\Exception $exp){

                    }
                }

                $data['file'] = $file;
                $kyc[$key] = $data;
            }
        }


        $deliveryman->kyc_data =     $kyc ;

        $deliveryman->save();


        return back()->with('success',translate('Updated successfully'));

    }


     /**
     * Update DeliveryMan status
     *
     * @param Request $request
     * @return string
     */
    public function passwordUpdate(Request $request)  {


        $request->validate([
            'id'=>'required|exists:delivery_men,id',
            'password'         => 'required|min:5|confirmed',
        ]);

        $deliveryman              = DeliveryMan::where('id',$request->input('id'))->firstOrfail();

        $deliveryman->password   =  Hash::make($request->password);
        $deliveryman->save();

        return back()->with('success', translate('Password Updated Successfully'));

    }



    /**
     * Update DeliveryMan status
     *
     * @param Request $request
     * @return string
     */
    public function statusUpdate(Request $request) :string {

        $request->validate([
            'data.id'=>'required|exists:delivery_men,id'
        ],[
            'data.id.required'=>translate('The Id Field Is Required')
        ]);
        $deliveryman              = DeliveryMan::where('id',$request->data['id'])->first();
        $response           = update_status($deliveryman->id,'DeliveryMan',$request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }



      /**
     * Update DeliveryMan status
     *
     * @param Request $request
     * @return string
     */
    public function kycStatusUpdate(Request $request) :string {

        $request->validate([
            'data.id'=>'required|exists:delivery_men,id'
        ],[
            'data.id.required'=>translate('The Id Field Is Required')
        ]);
        $deliveryman              = DeliveryMan::where('id',$request->data['id'])->first();
        $response                 = update_status(id: $deliveryman->id, 
                                                    modelName: 'DeliveryMan',
                                                    status: $request->data['status'],
                                                    columName:"is_kyc_verified");
        $response['reload'] = true;
        return json_encode([$response]);
    }





    public function delete($id){

        $deliveryman =  DeliveryMan::with(['ratings','orders'])->where('id',$id)->firstOrfail();

        if($deliveryman->image) remove_file(file_path()['profile']['delivery_man']['path'],$deliveryman->image);

        collect($deliveryman->kyc_data)->map(function(object $data , string $key): void{
            if($data->file)   remove_file(file_path()['delivery_man_kyc']['path'] ,$data->file);
        });

        $deliveryman->ratings()->delete();
        $deliveryman->delete();

        CustomerDeliverymanConversation::where('deliveryman_id',$deliveryman->id)
                                                ->lazyById(100,'id')
                                                ->each
                                                ->delete();

        return back()->with('success', translate('Deleted Successfully'));

    }


    public function orders($id){

        $deliveryman =  DeliveryMan::with(['ratings'])->where('id',$id)->firstOrfail();
        $title  = translate(keyWord: "All Orders");
        $orderStatus      = request()->input('status');

        $delevaryStatuses = Order::delevaryStatus();
        

        $orders = Order::whereHas('deliveryManOrder',fn(Builder $builder): builder =>
                                 $builder->where('deliveryman_id',$deliveryman->id)
                            )
                        ->search()
                        ->when( $orderStatus && Arr::exists(($delevaryStatuses),$orderStatus) , fn(Builder $q) => $q->where("status",Arr::get( $delevaryStatuses,$orderStatus)))
                        ->date()
                        ->physicalOrder()
                        ->orderBy('id', 'DESC')
                        ->with('customer',  'shipping', 'shipping.method','orderDetails', 'orderDetails.product')
                        ->paginate(site_settings('pagination_number',10))
                        ->appends(request()->all());

        return view('admin.delivery_man.order', compact('title', 'orders'));


    }



    /**
     * Summary of configuration
     * @return \Illuminate\View\View
     */
    public function configuration(): View{

        $title  = translate("Deliveryman configuration");
        return view('admin.delivery_man.configuration', compact('title'));

    }



      /**
     * get kyc settings
     * 
     * @return View
     */
    public function kycConfiguration() :View
    {
        return view('admin.delivery_man.kyc_configuration',[
            'title'       => 'KYC Configuration',
        ]);
    }


    /**
     * KYC settings
     *
     * @param Request $request
     * @return string
     */
    public function kycConfigurationUpdate(Request $request) :array {


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
                ['key'   =>  'deliveryman_kyc_settings'],
                ['value' =>  json_encode($promptInputs)]
            );
    
          } catch (\Exception $exception) {
     
            $message = $exception->getMessage();
         }



        Cache::forget(CacheKey::SITE_SETTINGS->value);

        return [
            'status'  => $status ,
            'message' => $message
        ];
        

    }




    /**
     * Summary of rewards
     * @return \Illuminate\View\View
     */
    public function rewards(): View{

        $title     = translate('Deliveryman rewards');
        $deliverymans = DeliveryMan::latest()->get();

        $rewardsLogs = RewardPointLog::with(['deliveryMan','product','order'])
                              ->search()
                              ->date()
                              ->whereNotNull('delivery_man_id')
                              ->paginate(site_settings('pagination_number',10))
                              ->appends(request()->all());
        
        return view('admin.delivery_man.rewards', compact('title', 'deliverymans','rewardsLogs'));

    }



     /**
     * Summary of referralLogs
     * @return \Illuminate\View\View
     */
    public function referralLogs(): View{

        $title     = translate('Deliveryman referral logs');
        $deliverymans = DeliveryMan::latest()->get();

        $refferelLogs = DeliveryMan::whereNotNull('referral_id')
                                        ->with(['refferedBy'])
                                        ->search()
                                        ->latest()
                                        ->date()
                                        ->paginate(site_settings('pagination_number',10))
                                        ->appends(request()->all());
        
        return view('admin.delivery_man.referral_logs', compact('title', 'deliverymans','refferelLogs'));

    }


     /**
     * Summary of balanceUpdate
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function balanceUpdate(Request $request): RedirectResponse{

        $request->validate([
            'id'      => 'required',
            'balance_type' => 'required|in:1,2',
            'amount'       => 'required|numeric|gt:0',
        ]);

        $user = DeliveryMan::findOrFail($request->input('id'));
        if($request->input('balance_type') == 1){
            $user->balance += $request->input('amount');
            $user->save();
            $transaction = Transaction::create([
                'deliveryman_id'     => $user->id,
                'amount'             => $request->input('amount'),
                'post_balance'       => $user->balance,
                'transaction_type'   => Transaction::PLUS,
                'transaction_number' => trx_number(),
                'details'            => 'Balance Added by admin',
            ]);
        }else{
            if($request->input('amount') >  $user->balance  )  return back()->with('error',translate('User Doesnot have enough balance to withdraw'));
            $user->balance -= $request->input('amount');
            $user->save();
            $transaction = Transaction::create([
                'deliveryman_id'            => $user->id,
                'amount'             => $request->input('amount'),
                'post_balance'       => $user->balance,
                'transaction_type'   => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details'            => 'Balance subtract by admin',
            ]);
        }

        return back()->with('success',translate('User balance has been updated'));
    }



}
