<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\OrderCollection;
use App\Http\Resources\Seller\OrderDetailsResource;
use App\Http\Resources\Seller\OrderResource;
use App\Http\Services\Seller\OrderService;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
class OrderController extends Controller
{


    protected ? Seller $seller;

    public function __construct(protected OrderService $orderService){
        $this->middleware(function ($request, $next) {
            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);
            return $next($request);
        });
    }
   

    /**
     * Get order list 
     *
     * @param string $type
     * @return JsonResponse
     */
    public function list(string $type) :JsonResponse {

        return api([ 
            'orders'                  => new OrderCollection($this->orderService->getOrderList($type ,$this->seller)),
        ])->success(__('response.success'));
      
    }

    /**
     * Get order details 
     *
     * @param string | int $orderNumber
     * @return JsonResponse
     */
    public function details(int | string $orderNumber) : JsonResponse {

            $response = $this->orderService->orderDetails($orderNumber ,$this->seller);


            $stauts   = Arr::get($response ,'status',false);

            switch (true) {
                case $stauts :
    
                    return api(
                        [
                            'order' =>new OrderResource(Arr::get($response,'order')),
                        ])->success(__('response.success'));
    
                    break;
                
                default:
                    return api(['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                    break;
            }

    }




    /**
     * Undocumented function
     *
     * @return JsonResponse
     */
    public function statusUpdate(Request $request) : JsonResponse {


        $validator = Validator::make($request->all(),[
            'order_number'                 => 'required|exists:orders,order_id',
            'status'                       => 'required|in:2,3,4,5,7',
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $response = $this->orderService->orderDetails($request->input("order_number") ,$this->seller);

        
        $stauts   = Arr::get($response ,'status',false);

        if(!$stauts)  return api(['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));

        $response = $this->orderService->updateOrderStatus(Arr::get($response ,'order') , $request ,$this->seller);
       
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
