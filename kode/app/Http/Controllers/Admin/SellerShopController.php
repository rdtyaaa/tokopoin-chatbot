<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Withdraw;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SellerShopController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:view_dashboard']);
    }

    public function index()
    {
        $title = translate('Seller shop');
        $bestSellers = Seller::where('best_seller_status', '2')->where('status', '1')->take(8)->get();
        $product['physical'] = Product::sellerProduct()->physical()->count();
        $product['digital'] = Product::sellerProduct()->digital()->count();

        $withdrawAmount = Withdraw::whereNotNull('seller_id')->where('status', Withdraw::SUCCESS)->sum('final_amount');
        $orders['digital_order'] = Order::sellerOrder()->digitalOrder()->count();

        $orders['placed'] = Order::physicalOrder()->placed()->sellerOrder()->count();
        $orders['processing'] = Order::physicalOrder()->processing()->sellerOrder()->count();
        $orders['shipped'] = Order::physicalOrder()->shipped()->sellerOrder()->count();
        $orders['delivered'] = Order::physicalOrder()->delivered()->sellerOrder()->count();

        $salesReport['month'] = collect([]);
        $salesReport['order_count'] = collect([]);

        $orderinfo = Order::sellerOrder()->physicalOrder()->selectRaw(DB::raw('count(*) as order_count'))
            ->selectRaw("DATE_FORMAT(created_at,'%M %Y') as months")
            ->groupBy('months')->get();

        $orderinfo->map(function ($query) use ($salesReport){
            $salesReport['month']->push($query->months);
            $salesReport['order_count']->push($query->order_count);
        });


        $monthlyOrderReport['monthly_order'] = collect([]);
        $orderReport = Order::sellerOrder()->physicalOrder()->whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('COUNT(CASE WHEN status = 1  THEN status END) AS placed')
            ->selectRaw('COUNT(CASE WHEN status = 2  THEN status END) AS confirmed')
            ->selectRaw('COUNT(CASE WHEN status = 3  THEN status END) AS processing')
            ->selectRaw('COUNT(CASE WHEN status = 4  THEN status END) AS shipped')
            ->selectRaw('COUNT(CASE WHEN status = 5  THEN status END) AS delivered')
            ->selectRaw('COUNT(CASE WHEN status = 6  THEN status END) AS cancel')->get()->toArray();
        $monthlyOrderReport = array_values($orderReport[0]);
        return view('admin.seller_shop', compact('title', 'orders', 'product', 'withdrawAmount', 'bestSellers', 'salesReport', 'monthlyOrderReport'));
    }
}
