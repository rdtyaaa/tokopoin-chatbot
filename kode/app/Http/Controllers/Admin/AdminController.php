<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Services\Admin\AdminService;
use App\Models\Admin;
use App\Models\Category;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\User;
use App\Models\Seller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Withdraw;
use App\Models\PaymentLog;
use App\Models\PlanSubscription;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Visitor;
use App\Rules\General\FileExtentionCheckRule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
class AdminController extends Controller
{


    public $adminService;
    public function __construct()
    {
        $this->adminService = new AdminService();
        $this->middleware(['permissions:view_dashboard'])->only('index');
        $this->middleware(['permissions:update_profile'])->only('profile','profileUpdate','passwordUpdate');
        $this->middleware(['permissions:view_admin'])->only('admin');
        $this->middleware(['permissions:create_admin'])->only('create','store');
        $this->middleware(['permissions:update_admin'])->only('update','edit','statusUpdate');
        $this->middleware(['permissions:delete_admin'])->only('destroy');
    }

    
    /**
     * Get Dashboard view 
     *
     * @return View
     */
    public function index() :View | RedirectResponse {


        if(request()->input('task')){
            $taskConfig = site_settings('task_config') 
                                    ? json_decode(site_settings('task_config'),true) 
                                    : [];
            if(!in_array(request()->input('task') ,$taskConfig)){
                array_push($taskConfig ,request()->input('task'));
            }
            Setting::updateOrInsert(
                ['key'    => 'task_config'],
                ['value'  => json_encode($taskConfig)]
            );


            Cache::forget(CacheKey::SITE_SETTINGS->value);
    

            return redirect()->route('admin.dashboard');
        }
   

        $year = request()->input('year',date("Y"));


        $data['product_by_year']           = sort_by_month(array_merge(...array_map(function ($month) {
            return [ key($month) => reset($month)];
        }, Product::inhouseProduct()->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total ,COUNT(CASE WHEN product_type = 101  THEN status END) AS digital,COUNT(CASE WHEN product_type = 102  THEN status END) AS physical")
                                    ->whereYear('created_at', $year)
                                    ->groupBy('months')
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            $item->months => [
                                                'total'    => $item->total,
                                                'digital'  => $item->digital,
                                                'physical' => $item->physical,
                                            ]
                                        ];
                                    })->toArray())),[
                                        'total'    => 0,
                                        'digital'  => 0,
                                        'physical' => 0,
                                    ]);
        

        $totalAmount = (int)PlanSubscription::join('pricing_plans', function ($join) {
            $join->on('plan_subscriptions.plan_id', '=', 'pricing_plans.id')
            ->where('plan_subscriptions.status', '!=', 3);
        })->sum('pricing_plans.amount');;


        $data['subscription_earning']      = $totalAmount;
        $data['order_payment']             = round(Order::where('payment_status', Order::PAID)->sum('amount'));
        $data['total_withdraw']            = round(Withdraw::where('status', PaymentLog::SUCCESS)->sum('amount'));



        $data['total_customer']            = User::count();
        $data['total_seller']              = Seller::count();
        $data['total_payment']             = round(Order::where('payment_status', Order::PAID)->sum('amount')) +  
                                             round(PaymentLog::where('status', PaymentLog::SUCCESS)->sum('charge'))+
                                             round(Withdraw::where('status', PaymentLog::SUCCESS)->sum('charge'))
                                             +      $totalAmount;
                                             
        $data['total_withdraw']            = Withdraw::where('status', Withdraw::SUCCESS)->sum('amount');
        $data['physical_product']          = Product::inhouseProduct()->physical()->whereYear('created_at', $year)->count();
       
        $data['digital_product']           = Product::inhouseProduct()->digital()->whereYear('created_at', $year)->count();
        $data['inhouse_order']             = Order::inhouseOrder()->physicalOrder()->count();
        $data['digital_order']             = Order::inhouseOrder()->digitalOrder()->count();
        $data['processing_order']          = Order::inhouseOrder()->physicalOrder()->processing()->count();
        $data['shipped_order']             = Order::inhouseOrder()->physicalOrder()->shipped()->count();
        $data['cancel_order']              = Order::inhouseOrder()->physicalOrder()->cancel()->count();
        $data['confirmed_order']           = Order::inhouseOrder()->physicalOrder()->confirmed()->count();
        $data['placed_order']              = Order::inhouseOrder()->physicalOrder()->placed()->count();
        $data['delivered_order']           = Order::inhouseOrder()->physicalOrder()->delivered()->count();
        

        $orderStatus                       = array_flip(Order::delevaryStatus());

        $data['monthly_order_report']      = Order::whereMonth('created_at', now()->month)
                                                ->selectRaw('COUNT(*) as count, status')
                                                ->groupBy('status')
                                                ->pluck('count', 'status')
                                                ->mapWithKeys(function ($count, $status) use($orderStatus) {
                                                    $status = Arr::get( $orderStatus , $status);
                                                    return [$status => $count];
                                                })->toArray();

        $data['earning_per_months']         = sort_by_month(Order::where('payment_status', Order::PAID)
                                                            ->selectRaw("MONTHNAME(created_at) as months, SUM(amount) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray());


        $data['monthly_payment_charge']     =  sort_by_month(PaymentLog::where('status', PaymentLog::SUCCESS)
                                                            ->selectRaw("MONTHNAME(created_at) as months, SUM(charge) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray());
                                                                
        $data['monthly_withdraw_charge']     =  sort_by_month(Withdraw::where('status', PaymentLog::SUCCESS)
                                                            ->selectRaw("MONTHNAME(created_at) as months, SUM(charge) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray());

        $data['top_categories']             =  Category::withCount('product')->top()->get();






       $data['order_by_year']               = sort_by_month(array_merge(...array_map(function ($month) {
                                                    return [ key($month) => reset($month)];
                                                }, Order::selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total ,COUNT(CASE WHEN order_type = 101 THEN 1 END) as digital, COUNT(CASE WHEN order_type = 102 THEN 1 END) as physical")
                                                ->whereYear('created_at', $year)
                                                ->groupBy('months')
                                                ->get()
                                                ->map(function ($item) {
                                                    return [
                                                        $item->months => [
                                                            'total'    => $item->total,
                                                            'digital'  => $item->digital,
                                                            'physical' => $item->physical,
                                                        ]
                                                    ];
                                                })->toArray())),[
                                                    'total'    => 0,
                                                    'digital'  => 0,
                                                    'physical' => 0,
                                                ]);

        





            $data['product_sell_by_month'] = sort_by_month(OrderDetails::selectRaw("MONTHNAME(created_at) as month, count(DISTINCT product_id) as total_products")
                            ->whereYear('created_at', '=', date("Y"))
                            ->groupBy('month')
                            ->pluck('total_products', 'month')
                            ->toArray());


       

            
   
        $data['latest_orders']             = Order::latest()->with(['customer','shipping'])->physicalOrder()->take(5)->get();


        $data['latest_transaction']        = Transaction::with(['user','seller'])->latest()->take(5)->get();
        $data['best_selling_product']      = Product::with(['category','order'])
                                                    ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                                    ->where('product_type', ProductType::PHYSICAL_PRODUCT)->where('best_selling_item_status',ProductStatus::BESTSELLING)
                                                    ->take(5)->get();


 

        $data['web_visitors'] = sort_by_month(Visitor::selectRaw("MONTHNAME(created_at) as month, count(*) as total")
                                                            ->whereYear('created_at', '=', date("Y"))
                                                            ->groupBy('month')
                                                            ->pluck('total', 'month')
                                                            ->toArray());


                        




  

        return view('admin.dashboard',[
            'title' => translate("Admin Dashboard"),
            'data'  => $data
        ]);
    }


   
    /**
     * Admin Profile
     *
     * @return View
     */
    public function profile() :View
    {
        return view('admin.profile', [
            'admin'   => auth_user(),
            'title'   => translate('Admin Profile')
        ]);
    }

    

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function profileUpdate(Request $request) :RedirectResponse
    {
        $size = explode('x', file_path()['profile']['admin']['size']);
        $admin = auth_user();
        $this->validate($request, [
            'name'       => 'required|max:255',
            'user_name'  => 'required|max:255|unique:admins,user_name,'. $admin ->id,
            'email'      => 'required|max:255|unique:admins,email,'. $admin->id,
            'image'      => [ new FileExtentionCheckRule(file_format())]
        ]);

        $admin->name      = $request->name;
        $admin->user_name = $request->user_name;
        $admin->phone     = $request->phone;
        $admin->email     = $request->email;
        if($request->hasFile('image')){
            try{
                $removefile = $admin->image ?: null;
                $admin->image = store_file($request->image, file_path()['profile']['admin']['path'], null, $removefile);
            }catch (\Exception $exp){
          
            }
        }
        $admin->save();
        return back()->with('success', translate("Profile Updated Successfully"));
    }


    

    /**
     * Update password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function passwordUpdate(Request $request) :RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:5|confirmed',
        ]);
        $admin = auth_user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', translate("Your Current Password does not match !!"));
        }
        $admin->password = Hash::make($request->password);
        $admin->save();
        
        return back()->with('success', translate("Password changed successfully"));
    }

    

    /**
     * Get all admins 
     *
     * @return View
     */
    public function admin() :View {
        return view('admin.admin.index', [
            'title'  =>  translate("Manage Staff"), 
            'admins' =>   $this->adminService->index(),
        ]);
    }
    

    /**
     * Create view of an admin staff
     *
     * @return View
     */
    public function create() :View
    {
        return view('admin.admin.create', [
            'title' =>  translate("Create Staff"),
            'roles' => Role::active()->get()
        ]);
    }
  
    
    /**
     * Store a admin staff
     *
     * @param AdminRequest $request
     * @return RedirectResponse
     */
    public function store(AdminRequest $request) :RedirectResponse {
        $response = $this->adminService->store($request);
        return back()->with($response['status'],$response['message']);
    }
  
    
    /**
     * Edit view of a admin 
     *
     * @param int | string $id
     * @return View
     */
    public function edit(int | string $id) :View {

        return view('admin.admin.edit', [
            'title'   => translate("Update Staff"),
            'admin'   =>  $this->adminService->admin($id),
            'roles'   => Role::active()->get()
        ]);
    }



    /**
     * Update admin 
     *
     * @param AdminRequest $request
     * @return RedirectResponse
     */
    public function update(AdminRequest $request) :RedirectResponse {
        $response = $this->adminService->update($request);
        return back()->with($response['status'],$response['message']);
    }




    /**
     * Update admin status
     *
     * @param Request $request
     * @return string
     */
    public function statusUpdate(Request $request) :string {

        $request->validate([
            'data.id'=>'required|exists:admins,id'
        ],[
            'data.id.required'=>translate('The Id Field Is Required')
        ]); 
        $admin              = Admin::where('id',$request->data['id'])->first();
        $response           = update_status($admin->id,'Admin',$request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }
  
    
    

    /**
     * Destroy a admin
     * 
     * @param int | string $id
     * 
     * @return RedirectResponse
     * 
     */
    public function destroy(int | string $id) :RedirectResponse {
        $response = $this->adminService->destory($id);
        return back()->with( $response['status'],$response['message']);
    }


    
}
