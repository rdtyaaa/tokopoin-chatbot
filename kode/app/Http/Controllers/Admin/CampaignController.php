<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampaignController extends Controller
{

    public function __construct(){

        $this->middleware(['permissions:manage_campaign']);
    }

    public function index() :View
    {
        $title         = translate('Manage Campaign');
        $campaigns     = Campaign::search()->with(['products'])->latest()->get();
        $paymentMehods = PaymentMethod::where('status',1)->get();
        return view('admin.campaign.index', compact('title', 'campaigns','paymentMehods'));
    }

    public function create() :View
    {
        $title = translate('Create Campaign');
        $categories =  Category::with(['product'=>function($q){
            return $q->physical()->published();

        }])->where('status','1')->get();

        $paymentMehods = PaymentMethod::where('status',1)->get();
        return view('admin.campaign.create', compact('title','categories','paymentMehods'));
    }

    public function store(Request $request) :RedirectResponse
    {
        $products = [];
        $validation = [
            'product'      => 'required',
            'name'         => 'required|unique:campaigns,name',
            'banner_image' => ['required','image',new FileExtentionCheckRule(file_format())],
            'strat_date'   => 'required|before_or_equal:end_date',
            'end_date'     => 'required|after_or_equal:strat_date'
        ];

        if($request->discount){
            $validation['discount_type'] = 'required';

        }
        $banner = '';
        $this->validate($request, $validation,['product.required'=>'Please Select Some Product For Campaign']);
        if($request->hasFile('banner_image')) {
            try {
                $banner = store_file($request->banner_image, file_path()['campaign_banner']['path']);
            }catch (\Exception $exp) {

            }
        }

        $campaign = Campaign::create([
            'payment_method' => json_encode($request->payment_method),
            'name'           => $request->name,
            'slug'           => Str::slug($request->name),
            'banner_image'   => $banner,
            'start_time'     => $request->strat_date,
            'end_time'       => $request->end_date,
            'discount_type'  => $request->discount_type,
            'discount'       => $request->discount,
            'show_home_page' => $request->input('show_home_page',0),
            'status'         => $request->status,

        ]);

        foreach(array_values($request->product) as $key=>$val){
            if($val['discount_type'] == null ){
               $val['discount_type'] = $request->discount_type ? $request->discount_type : '0';
            }
            if($val['discount']   == null || $val['discount'] == 0 ){
                $val['discount']  = $request->discount ? $request->discount : 0;
            }
            if($val['discount_type'] == '1' && $val['discount'] >100 ){
                $val['discount']     = 100;
            }
            array_push($products,$val);
        }

        $campaign->products()->attach($products);
        return back()->with('success', translate("Campaign has been created"));;
    }


    public function edit(int $id) :View
    {
        $paymentMehods = PaymentMethod::where('status',1)->get();
        $title      = translate('Campaign Update');
        $campaign   = Campaign::with(['products'])->where('id', $id)->first();
        $categories =  Category::with(['product'=>function($q){
            return $q->physical()->published();

        }])->where('status','1')->get();
        return view('admin.campaign.edit', compact('title', 'campaign','paymentMehods','categories'));
    }


    public function update(Request $request) :RedirectResponse
    {
        $campaign = Campaign::with('products')->where('id', $request->id)->first();
        $products = [];
        $validation = [
            'product'      => 'required',
            'name'         => 'required|unique:campaigns,name,'.$request->id,
            'banner_image' => ['image',new FileExtentionCheckRule(file_format())],
            'strat_date'   => 'required|before_or_equal:end_date',
            'end_date'     => 'required|after_or_equal:strat_date'
        ];

        if($request->discount){
            $validation['discount_type'] = 'required';

        }
        $this->validate($request, $validation,['product.required'=>'Please Select Some Product For Campaign']);
        if($request->hasFile('banner_image')) {
            try {
                $banner = store_file($request->banner_image, file_path()['campaign_banner']['path'],null ,  $campaign->banner_image);
                $campaign->banner_image =  $banner ;
            }catch (\Exception $exp) {

            }
        }

        $campaign->payment_method = json_encode($request->payment_method);
        $campaign->name           =  $request->name;
        $campaign->slug           =  Str::slug( $request->name);
        $campaign->start_time     =  $request->strat_date;
        $campaign->end_time       =  $request->end_date;
        $campaign->discount_type  =  $request->discount_type;
        $campaign->discount       =  $request->discount;
        $campaign->status         =  $request->status;
        $campaign->show_home_page =  $request->input('show_home_page',0);

        $campaign->save();

        foreach(array_values($request->product) as $key=>$val){
            if($val['discount_type'] == null ){
               $val['discount_type'] = $request->discount_type ? $request->discount_type : '0';
            }
            if($val['discount'] == null || $val['discount'] == 0 ){
                $val['discount'] = $request->discount ? $request->discount : 0;
            }
            if($val['discount_type'] == '1' && $val['discount'] >100 ){
                $val['discount']  = 100;
            }
            array_push($products,$val);
        }
        $campaign->products()->detach();
        $campaign->products()->attach($products);
        return back()->with('success', translate("Campaign has been Updated"));
    }



}
