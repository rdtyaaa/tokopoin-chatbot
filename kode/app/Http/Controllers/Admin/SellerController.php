<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Models\Order;
use App\Models\SellerShopSetting;
use App\Models\SupportTicket;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\MinMaxCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:view_seller']);
    }

    public function index() :View
    {
        $title    = translate('Manage seller');
        $sellers  = Seller::search()->latest()->with('product', 'sellerShop')->paginate(site_settings('pagination_number',10));
        return view('admin.seller.index', compact('title', 'sellers'));
    }

    public function active() :View
    {
        $title   = translate('Active seller');
        $sellers = Seller::search()->where('status', '1')->with('product', 'sellerShop')->latest()->paginate(site_settings('pagination_number',10));
        return view('admin.seller.index', compact('title', 'sellers'));
    }

    public function banned() :View
    {
        $title   = translate('Banned seller');
        $sellers = Seller::search()->where('status', '0')->with('product', 'sellerShop')->latest()->paginate(site_settings('pagination_number',10));
        return view('admin.seller.index', compact('title', 'sellers'));
    }

    public function shop(int $id) :View
    {
        $title   = translate('Seller Shop');
        $seller  = Seller::findOrFail($id);
        return view('admin.seller.shop', compact('title', 'seller'));
    }

    public function shopUpdate(Request $request, int $id) :RedirectResponse
    {

        $seller     = Seller::findOrFail($id);

        $sellerShop = SellerShopSetting::where('seller_id', $seller->id)->first();


        if(!$sellerShop){
            $sellerShop  = new SellerShopSetting();
            $sellerShop->seller_id =  $seller->id;
        }

        $this->validate($request, [
            'name' => 'required|max:255|unique:seller_shop_settings,name,'.$sellerShop->id,
            'email' => 'nullable|email|unique:seller_shop_settings,email,'.$sellerShop->id,
            'phone' => 'required|unique:seller_shop_settings,phone,'.$sellerShop->id,
            'shop_logo' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'shop_first_image' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'seller_site_logo' => ['nullable','image',new FileExtentionCheckRule(file_format()),],
            'seller_site_logo_sm' => ['nullable','image',new FileExtentionCheckRule(file_format()),],
            'whatsapp_number' => ['required'],
            'whatsapp_order'   => ['required',Rule::in(array_values(StatusEnum::toArray()))],
             'status' => 'required|in:1,2'
        ]);



        $shopLogo = $sellerShop->shop_logo;
        $sellerSiteLogo = $sellerShop->seller_site_logo ?? 'default.png';
        $logoIcon       = $sellerShop->logoicon;
        $shop_first_image = $sellerShop->shop_first_image;

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




        $sellerShop->status = $request->status;
        $sellerShop->name = $request->name;
        $sellerShop->whatsapp_number = $request->whatsapp_number;
        $sellerShop->whatsapp_order  = $request->whatsapp_order;
        $sellerShop->email = $request->email;
        $sellerShop->phone = $request->phone;
        $sellerShop->address = $request->address;
        $sellerShop->short_details = $request->short_details;
        $sellerShop->shop_logo = $shopLogo;
        $sellerShop->seller_site_logo = $sellerSiteLogo;
        $sellerShop->logoicon = $logoIcon;
        $sellerShop->shop_first_image = $shop_first_image;
        $sellerShop->save();

        
        return back()->with('success',translate('Seller shop status has been updated'));
    }

    public function details(int $id) :View
    {
        $title  = translate('Seller details');
        $seller = Seller::findOrFail($id);
        $orders['count']    = Order::specificSellerOrder($id)->count();
        $orders['physical'] = Order::specificSellerOrder($id)->physicalOrder()->orderBy('id', 'DESC')->get();
        $orders['digital']  = Order::specificSellerOrder($id)->digitalOrder()->orderBy('id', 'DESC')->count();
        return view('admin.seller.detail', compact('title', 'seller', 'orders'));
    }


    public function login(int $id) :RedirectResponse
    {

        $seller = Seller::findOrFail($id);
        Auth::guard('seller')->login($seller);

        return redirect()->route('seller.dashboard');

    }

    public function update(Request $request, int $id) :RedirectResponse
    {
        $request->validate([
            'name'     => 'nullable|max:120',
            'email'    => 'nullable|unique:sellers,email,'.request()->route('id'),
            'phone'    => 'nullable|unique:sellers,phone,'.request()->route('id'),
            'address'  => 'nullable|max:250',
            'city'     => 'nullable|max:250',
            'state'    => 'nullable|max:250',
            'zip'      => 'nullable|max:250',
            'rating'   => 'gt:0|lte:5',
            'status'   => 'nullable|in:1,2',
            'kyc_status'   => 'required|in:1,0',
        ]);
        $seller         = Seller::findOrFail($id);
        $seller->name   = $request->name;
        $seller->email  = $request->email;
        $seller->phone  = $request->phone;
        $address = [
            'address' => $request->address,
            'city'    => $request->city,
            'state'   => $request->state,
            'zip'     => $request->zip
        ];
        $seller->address = $address;
        $seller->status  = $request->status;
        $seller->rating  = $request->rating;
        $seller->kyc_status  = $request->kyc_status;
        $seller->save();
        return back()->with('success',translate('Seller has been updated'));
    }

    public function sellerAllProduct(int $id) :View
    {
        $seller   = Seller::findOrFail($id);
        $title    = ucfirst($seller->name)." products";
        $products = Product::sellerProduct()->physical()->where('seller_id', $id)->latest()->with('seller', 'category', 'order')->paginate(site_settings('pagination_number',10));
        return view('admin.seller_product.index', compact('title', 'products'));
    }

    public function sellerTransaction(int $id) :View
    {
        $seller = Seller::findOrFail($id);
        $title  = ucfirst($seller->name) ." transaction";
        $transactions = Transaction::sellers()->where('seller_id', $id)->latest()->with('seller')->paginate(site_settings('pagination_number',10));
        return view('admin.report.index', compact('title', 'transactions'));
    }

    public function sellerWithdraw(int $id) :View
    {
        $seller    = Seller::findOrFail($id);
        $title     = ucfirst($seller->name) ." all withdraw log";
        $withdraws = Withdraw::where('status', '!=', 0)->where('seller_id', $id)->latest()->with('method', 'seller')->paginate(site_settings('pagination_number',10));
        return view('admin.withdraw.index', compact('title', 'withdraws'));
    }

    public function sellerAllDigitalProduct(int $id) :View
    {
        $seller = Seller::findOrFail($id);
        $title  = ucfirst($seller->name)." digital products";
        $inhouseDigitalProducts = Product::with('category')->sellerProduct()->where('seller_id', $id)->digital()->latest()->with('seller')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.index', compact('title', 'inhouseDigitalProducts'));
    }

    public function sellerDigitalProductOrder(int $id) :View
    {
        $seller = Seller::findOrFail($id);
        $title = ucfirst($seller->name)." digital product orders";
        $orders = Order::sellerOrder()->digitalOrder()->whereHas('digitalProductOrder',function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_order.index', compact('title', 'orders'));
    }

    public function sellerPhysicalProductOrder(int $id) :View
    {
        $seller = Seller::findOrFail($id);
        $title  = ucfirst($seller->name)."  product orders";
        $orders = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails',function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.seller_order.index', compact('title', 'orders'));
    }

    public function ticket(int $id) :View
    {
        $user = Seller::findOrFail($id);
        $title = ucfirst($user->name)." all support tickets";
        $supportTickets = SupportTicket::where('seller_id', $id)->latest()->paginate(site_settings('pagination_number',10));
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }




    public function bestSeller(int $id) :RedirectResponse
    {
        $seller = Seller::findOrFail($id);
        $seller->best_seller_status = $seller->best_seller_status == 1 ? 2 : 1;
        $seller->save();

        return back()->with('success',translate('Best seller status has been updated'));
    }


    public function sellerBalanceUpdate(Request $request) :RedirectResponse
    {
        $request->validate([
            'seller_id'    => 'required',
            'balance_type' => 'required|in:1,2',
            'amount'       => 'required|numeric|gt:0',
        ]);
        $seller = Seller::findOrFail($request->seller_id);
        if($request->balance_type == 1){
            $seller->balance += $request->amount;
            $seller->save();
            $transaction = Transaction::create([
                'seller_id'          => $seller->id,
                'amount'             => $request->amount,
                'post_balance'       => $seller->balance,
                'transaction_type'   => Transaction::PLUS,
                'transaction_number' => trx_number(),
                'details'            => 'Balance Added by admin',
            ]);
        }else{
            if($request->amount >  $seller->balance  ){
                return back()->with('error',translate('Seller Doesnot have enough balance to withdraw'));
            }
            $seller->balance -= $request->amount;
            $seller->save();
            $transaction = Transaction::create([
                'seller_id'          => $seller->id,
                'amount'             => $request->amount,
                'post_balance'       => $seller->balance,
                'transaction_type'   => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details' => 'Balance subtract by admin',
            ]);
        }

        return back()->with('success',translate('Seller balance has been updated'));
    }








    /**
     * Summary of delete
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int | string $id): RedirectResponse{

        $seller = Seller::with(['sellerShop'])->findOrFail($id);
        if($seller->sellerShop) $this->destroyShop($seller->sellerShop);
        if($seller->image)      remove_file(file_path()['profile']['seller']['path'],$seller->image);
        $seller->delete();
        return back()->with("success",'Shop deleted successfully');
    }



    /**
     * Summary of destroyShop
     * @param \App\Models\SellerShopSetting $shop
     * @return bool
     */
    public function destroyShop(SellerShopSetting $shop): bool{

        try {

            if($shop->shop_logo)         remove_file(file_path()['shop_logo']['path'],$shop->shop_logo);
            if($shop->seller_site_logo)  remove_file(file_path()['seller_site_logo']['path'],$shop->seller_site_logo);
            if($shop->logoicon)          remove_file(file_path()['seller_site_logo']['path'],$shop->logoicon);
            if($shop->shop_first_image)  remove_file(file_path()['shop_first_image']['path'],$shop->shop_first_image);
            $shop->delete();
            return true;
       
        } catch (\Throwable $th) {return false;}
    }




    /**
     * Summary of create
     * @return \Illuminate\View\View
     */
    public function create() :View{
        $title = translate("Seller create");
        return view('admin.seller.create', compact('title'));
    }




    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse{

        $request->validate([
            'name'     => 'required|max:120',
            'email'    => 'required|unique:sellers,email',
            'username' => 'required|unique:sellers,username',
            'phone'    => 'required|unique:sellers,phone',
            'address'  => 'nullable|max:250',
            'city'     => 'nullable|max:250',
            'state'    => 'nullable|max:250',
            'zip'      => 'nullable|max:250',
            'password'      => 'required',
            'image'    => [ 'nullable', new FileExtentionCheckRule(file_format())]
        ]);
        $seller         =  new Seller();
        $seller->name   = $request->input('name');
        $seller->email  = $request->input('email');
        $seller->phone  = $request->input('phone');
        $seller->username  = $request->input('username');
        $seller->password  = Hash::make($request->input('password'));
        $address = [
            'address' => $request->input('address'),
            'city'    => $request->input('city'),
            'state'   => $request->input('state'),
            'zip'     => $request->input('zip')
        ];
        $seller->address = $address;
        $seller->status  = Status::ACTIVE;

        if($request->hasFile('image')){
            try{
                $seller->image = store_file($request->image, file_path()['profile']['seller']['path']);
            }catch (\Exception $exp){
          
            }
        }
        $seller->save();

        return back()->with("success",'Seller created successfully');
    }
}
