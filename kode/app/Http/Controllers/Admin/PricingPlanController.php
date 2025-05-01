<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingPlan;
use App\Models\PlanSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PricingPlanController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:view_seller']);
    }

    public function index()
    {
        $title = translate('Manage pricing plan');
        $plans = PricingPlan::search()->latest()->paginate(site_settings('pagination_number',10));
        return view('admin.plan.index', compact('title', 'plans'));
    }

    public function store(Request $request) :RedirectResponse
    {
        $data = $this->validate($request, [
            'name'          => 'required|max:255',
            'amount'        => 'required|numeric',
            'total_product' => 'required|integer|gt:0',
            'duration'      => 'required|integer|gt:0',
            'status'        => 'required|in:1,2',
        ]);
        PricingPlan::create($data);

        return back()->with('success',translate('Pricing plan has been created'));

    }

    public function update(Request $request) :RedirectResponse
    {
        $data = $this->validate($request, [
            'name'          => 'nullable|max:255',
            'amount'        => 'required|numeric',
            'total_product' => 'required|integer|gt:0',
            'duration'      => 'required|integer|gt:0',
            'status'        => 'required|in:1,2',
        ]);
        $plan = PricingPlan::findOrFail($request->id);
        $plan->update($data);
        return back()->with('success',translate('Pricing plan has been updated'));
    }

    public function delete(Request $request) :RedirectResponse
    {
        $pricingPlan = PricingPlan::where('id', $request->id)->firstOrfail();
        if(count($pricingPlan->plansubcription) > 0)
        {
            return back()->with('success',translate('Before delete subcription plan and try again'));
        }

        $pricingPlan->delete();
        return back()->with('success',translate('Plan has been deleted'));

    }

    public function subscription() :View
    {
        $title         = translate('Subscription seller log');
        $subscriptions = PlanSubscription::search()->orderBy('id', 'DESC')->with('seller', 'plan')->paginate(site_settings('pagination_number',10));
        return view('admin.plan.subscription', compact('title', 'subscriptions'));
    }


    public function subscriptionApproved(Request $request) :RedirectResponse
    {
        $planSubscription = PlanSubscription::where('id',$request->id)->first();
        $sellerPlan       = PlanSubscription::where('seller_id',$planSubscription->seller_id)->whereIn('status', [1,2])->first();
        if($sellerPlan){
            $sellerPlan->status = 4;
            $sellerPlan->save();
        }
        $planSubscription->status = 1;
        $planSubscription->save();
        return back()->with('success',translate("Subscription plan has been updated"));
    }



}
