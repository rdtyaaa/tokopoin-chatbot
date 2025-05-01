<?php

namespace App\Http\Controllers\Api\Delivery\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Deliveryman\Auth\LoginRequest;
use App\Http\Services\Deliveryman\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    


    public function __construct(protected AuthService $authService){

        
    }


    /**
     * Login a new deliveryman 
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)  : JsonResponse {

        $deliveryman = $this->authService->getActiveDeliverymanByPhone($request->input("phone_code"),$request->input("phone"));

        switch (true) {
            case $deliveryman && Hash::check($request->input("password"),$deliveryman->password ):
                        return api([
                            'access_token' => $this->authService->getAccessToken($deliveryman)
                        ])->success(__('response.success'));
                break;
            
            default:
                   return api(['errors' => ['Credentail Mismatch !!']])->fails(__('response.failed'));
                break;
        }
    }



  




}
