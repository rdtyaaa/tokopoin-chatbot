<?php

namespace App\Http\Controllers\Api\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\Auth\LoginRequest;
use App\Http\Services\Seller\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    


    public function __construct(protected AuthService $authService){

        
    }


    /**
     * Login a new seller 
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)  : JsonResponse {

        $seller = $this->authService->getActiveSellerByUsername($request->input("username"));

        switch (true) {
            case $seller && Hash::check($request->input("password"),$seller->password ):
                        return api([
                            'access_token' => $this->authService->getAccessToken($seller)
                        ])->success(__('response.success'));
                break;
            
            default:
                   return api(['errors' => ['Credentail Mismatch !!']])->fails(__('response.failed'));
                break;
        }
    }



  




}
