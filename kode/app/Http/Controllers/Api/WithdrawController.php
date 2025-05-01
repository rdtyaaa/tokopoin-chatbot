<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\WithdrawPreviewRequest;
use App\Http\Resources\Seller\WithdrawCollection;
use App\Http\Resources\Seller\WithdrawMethodCollection;
use App\Http\Resources\Seller\WithdrawResource;
use App\Http\Services\User\WithdrawService;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WithdrawController extends Controller
{


    protected ? User $user;

    public function __construct(protected WithdrawService $withdrawService){
        $this->middleware(function ($request, $next) {
            if(site_settings('customer_wallet') == StatusEnum::false->status()) return api(['errors'=> translate('Wallet system is no incative') ])->fails(__('response.fail'));

            $this->user = auth()->guard('api')->user()?->load(['country','billingAddress']);  
            return $next($request);
        });
    }





    /**
     * Get withdraw methods
     *
     * @return JsonResponse
     */
    public function methods() : JsonResponse {

        return api([ 
            'methods' => new WithdrawMethodCollection($this->withdrawService->getMethod())
        ])->success(__('response.success'));
    
    }


    public function list() : JsonResponse {
        return api(['withdraw_list' => new WithdrawCollection($this->withdrawService->getPaginatedList($this->user))])->success(__('response.success'));
    }



    /**
     * Withdraw request create 
     *
     * @param WithdrawPreviewRequest $request
     * @return JsonResponse
     */
    public function request(WithdrawPreviewRequest $request) : JsonResponse {


         $response = $this->withdrawService->createRequest($request ,$this->user );

         $stauts   = Arr::get($response ,'status',false);


         switch (true) {
            case $stauts :

                return api(
                    [
                        'withdraw' => new WithdrawResource(Arr::get($response ,'withdraw')->load(['currency','method'])),
                        'message'  => Arr::get($response ,'message'),
                    ])->success(__('response.success'));

                break;
            
            default:
                return api(['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                break;
        }

    }





    /**
     * Withdraw store
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse {


        $response = $this->withdrawService->store($request ,$this->user );

        $stauts   = Arr::get($response ,'status',false);

        switch (true) {
           case $stauts :

               return api(
                   [
                       'message'  => Arr::get($response ,'message'),
                   ])->success(__('response.success'));

               break;
           
           default:
               return api(['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
               break;
       }


    }









}
