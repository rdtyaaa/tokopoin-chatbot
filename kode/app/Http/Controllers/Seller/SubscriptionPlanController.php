<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingPlan;
use App\Models\PlanSubscription;
use App\Models\Transaction;
use Carbon\Carbon;

class SubscriptionPlanController extends Controller
{

    public function index()
    {
        $title = translate('Subscription History');
        $seller = auth()->guard('seller')->user();
        $plans = PricingPlan::where('status',1)->orderBy('amount', 'DESC')->get();
        $subscriptions = PlanSubscription::date()->where('seller_id', $seller->id)->with('plan')->latest()->paginate(site_settings('pagination_number',10));
        return view('seller.plan.index', compact('title', 'subscriptions', 'plans'));
    }
    public function plan()
    {
        $seller = auth()->guard('seller')->user();

        $title = "Subscription plan";
        $plans = PricingPlan::where('status',1)->latest()->get();
        return view('seller.plan.create', compact('title', 'plans'));
    }


    public function subscription(Request $request)
    {

        $this->validate($request, ['id' => 'required|exists:pricing_plans,id']);
        $seller = auth()->guard('seller')->user();
        $plan = PricingPlan::where('id', $request->id)->where('status', 1)->firstOrFail();
        $planSubscription = PlanSubscription::where('plan_id', $request->id)->where('seller_id', $seller->id)->first();

        if($plan->name == 'Free' && @$planSubscription->plan->name == 'Free'){
            return back()->with('error',translate("You Cant Get Free Plan More Than once"));
        }
        if($plan->amount > 0){
            if($plan->amount > $seller->balance) {
                return back()->with('error',translate("You do not have a sufficient balance for subscribing."));
            }
            $seller->balance -= $plan->amount;
            $seller->save();
            Transaction::create([
                'seller_id' => $seller->id,
                'amount' => $plan->amount,
                'post_balance' => $seller->balance,
                'transaction_type' => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details' => 'Subscription ' .$plan->name. ' plan',
            ]);
        }

        PlanSubscription::where('seller_id',$seller->id)->update([
            'status'    => PlanSubscription::EXPIRED,
        ]);

        PlanSubscription::create([
            'seller_id' => $seller->id,
            'plan_id'   => $request->id,
            'total_product'   => $plan->total_product,
            'expired_date' => Carbon::now()->addDays($plan->duration),
            'status'    => PlanSubscription::RUNNING,
        ]);
        return redirect()->route('seller.plan.index')->with('success',translate("Plan has been subscribe"));
    }

    public function planUpdateRequest(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:pricing_plans,id']);
        $seller           = auth()->guard('seller')->user();
        $planSubscription = PlanSubscription::where('status',PlanSubscription::RUNNING)->where('plan_id', $request->id)->where('seller_id', $seller->id)->first();
        $plan = PricingPlan::where('id', $request->id)->where('status', 1)->firstOrFail();

        if($plan->name == 'Free' && @$planSubscription->plan->name == 'Free'){
            return back()->with('error',translate("You Cant Get Free Plan More Than once"));
        }
        if($planSubscription){
            return back()->with('error',translate("You already had a running subscription with this plan"));
        }
        if($plan->amount > 0){
            if($plan->amount > $seller->balance) {
                return back()->with('error',translate("You do not have a sufficient balance for subscribing."));
            }
            $seller->balance -= $plan->amount;
            $seller->save();
            Transaction::create([
                'seller_id' => $seller->id,
                'amount' => $plan->amount,
                'post_balance' => $seller->balance,
                'transaction_type' => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details' => 'Subscription ' .$plan->name. ' plan',
            ]);
        }
        PlanSubscription::create([
            'seller_id' => $seller->id,
            'plan_id'   => $request->id,
            'total_product'   => $plan->total_product,
            'expired_date' => Carbon::now()->addDays($plan->duration),
            'status'    => PlanSubscription::REQUESTED,
        ]);
        return back()->with('success',translate("Plan update request has been submitted"));
    }


    public function renewPlan(Request $request)
    {
        $seller = auth()->guard('seller')->user();
        $planSubscription = PlanSubscription::where('id', $request->id)->where('seller_id', $seller->id)->firstOrFail();
        if(@$planSubscription->plan->name == 'Free'){
            return back()->with('error',translate("You Cant Not Renew Free Plan Twice"));
        }
        if($planSubscription->plan->amount > $seller->balance) {
            return back()->with('error',translate("You do not have a sufficient balance"));
        }
        $seller->balance -= $planSubscription->plan->amount;
        $seller->save();
        Transaction::create([
            'seller_id' => $seller->id,
            'amount' => $planSubscription->plan->amount,
            'post_balance' => $seller->balance,
            'transaction_type' => Transaction::MINUS,
            'transaction_number' => trx_number(),
            'details' => 'Subscription ' .$planSubscription->plan->name. ' plan',
        ]);

        $planSubscription->expired_date = $planSubscription->expired_date->addDays($planSubscription->plan->duration);
        $planSubscription->total_product = $planSubscription->plan->total_product;

        $planSubscription->status = 1;
        $planSubscription->save();
        return back()->with('success',translate("Plan has been renewed"));
    }


}
