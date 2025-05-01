<?php

namespace App\Http\Controllers\Api\Delivery\Auth;

use App\Enums\Status;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Deliveryman\Auth\LoginRequest;
use App\Http\Requests\Api\Deliveryman\Auth\RegisterRequest;
use App\Http\Services\Deliveryman\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    


    public function __construct(protected AuthService $authService){



        $this->middleware(function ($request, $next) {
            if(site_settings('deliveryman_registration') != StatusEnum::true->status()) return api(['errors' => [translate('Registration is currently off.')]])->fails(__('response.error'));
            return $next($request);
        });




        
    }


    /**
     * register a new deliveryman 
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)  : JsonResponse {


        $deliveryman = $this->authService->register($request);

        if($deliveryman){
            return api([
                'access_token' => $this->authService->getAccessToken($deliveryman)
            ])->success(__('response.success'));
        }

        return api(['errors' => ['Invalid payload']])->fails(__('response.failed'));
    }



  




}
