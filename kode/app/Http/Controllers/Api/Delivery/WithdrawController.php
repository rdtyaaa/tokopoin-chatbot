<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\WithdrawPreviewRequest;
use App\Http\Resources\Seller\WithdrawCollection;
use App\Http\Resources\Seller\WithdrawMethodCollection;
use App\Http\Resources\Seller\WithdrawResource;
use App\Http\Services\Deliveryman\WithdrawService as DeliverymanWithdrawService;
use App\Models\DeliveryMan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WithdrawController extends Controller
{



    protected ? DeliveryMan $deliveryman;
    public function __construct(protected DeliverymanWithdrawService $deliverymanWithdrawService){
        $this->middleware(function ($request, $next) {
            $this->deliveryman = auth()->guard('delivery_man:api')->user()?->load(['ratings','orders','refferedBy']);
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
            'methods' => new WithdrawMethodCollection($this->deliverymanWithdrawService->getMethod())
        ])->success(__('response.success'));

    }


    public function list() : JsonResponse {
        return api(['withdraw_list' => new WithdrawCollection($this->deliverymanWithdrawService->getPaginatedList($this->deliveryman))])->success(__('response.success'));
    }



    /**
     * Withdraw request create
     *
     * @param WithdrawPreviewRequest $request
     * @return JsonResponse
     */
    public function request(WithdrawPreviewRequest $request) : JsonResponse {

         $response = $this->deliverymanWithdrawService->createRequest($request ,$this->deliveryman );

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


        $response = $this->deliverymanWithdrawService->store($request ,$this->deliveryman );

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
