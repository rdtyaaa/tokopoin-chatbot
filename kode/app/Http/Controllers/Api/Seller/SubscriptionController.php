<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\SubscriptionRequest;
use App\Http\Resources\Seller\PlanCollection;
use App\Http\Resources\Seller\SubscriptionCollection;
use App\Http\Services\Seller\SellerService;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SubscriptionController extends Controller
{
    protected ? Seller $seller;

    public function __construct(protected SellerService $sellerService){
        $this->middleware(function ($request, $next) {
            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);
            return $next($request);
        });
    }



    /**
     * Get subscription plan
     *
     * @return JsonResponse
     */
    public function plan() : JsonResponse {
        return api([ 
            'plans' => new PlanCollection($this->sellerService->getSubscriptionPlan())
        ])->success(__('response.success'));
    }



    /**
     * Get seller subscriptions
     *
     * @return JsonResponse
     */
    public function list() : JsonResponse {
        return api([ 
            'subscriptions' => new SubscriptionCollection($this->sellerService->getPaginatedSubscription($this->seller))
        ])->success(__('response.success'));
    }






    /**
     * Renew a new subscription
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function renew(string $uid) : JsonResponse {

        $subscriptions    = $this->sellerService->getSubscriptionByUid($uid ,$this->seller);

        if(!$subscriptions) return api(['errors'=> [translate("Invalid subscription")]])
        ->fails(__('response.fail'));

        $response = $this->sellerService->renewSubscription($subscriptions ,$this->seller);

        $stauts   = Arr::get($response ,'status',false);

        switch (true) {
            case $stauts :

                return api(
                    [
                        'message' => Arr::get($response ,'message'),
                    ])->success(__('response.success'));

                break;
            
            default:
    
                return api(
                    ['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                break;
        }


    }





    /**
     * Subscribe to a new plan
     *
     * @param SubscriptionRequest $request
     * @return JsonResponse
     */
    public function subscribe(SubscriptionRequest $request) : JsonResponse {

        $subscriptions    = $this->sellerService->getPaginatedSubscription($this->seller);

        if(!$subscriptions->isEmpty()) return api(['errors'=> [translate("Please request for a new subscription!! You are already a subscribers")]])
        ->fails(__('response.fail'));

        $plan          =  $this->sellerService->getPlanById($request->input("id"));

        if(!$plan)  return api(['errors'=> [translate("Plan not found")]])
        ->fails(__('response.fail'));


        $response = $this->sellerService->createSubscription($plan ,$this->seller);

        $stauts   = Arr::get($response ,'status',false);


        switch (true) {
            case $stauts :

                return api(
                    [
                        'message' => Arr::get($response ,'message'),
                    ])->success(__('response.success'));

                break;
            
            default:
                return api(['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));

                break;
        }



    }





    /**
     * Subscription plan update request
     *
     * @param SubscriptionRequest $request
     * @return JsonResponse
     */
    public function update(SubscriptionRequest $request) : JsonResponse {
        
        $plan          =  $this->sellerService->getPlanById($request->input("id"));

        if(!$plan)  return api(['errors'=> [translate("Plan not found")]])
        ->fails(__('response.fail'));



        $response = $this->sellerService->updateSubscription($plan ,$this->seller);


        $stauts   = Arr::get($response ,'status',false);


        switch (true) {
            case $stauts :

                return api(
                    [
                        'message' => Arr::get($response ,'message'),
                    ])->success(__('response.success'));

                break;
            
            default:
              return api(
                [
                    'message' => Arr::get($response ,'message'),
                ])->fails(__('response.fail'));
                break;
        }

    }


}
