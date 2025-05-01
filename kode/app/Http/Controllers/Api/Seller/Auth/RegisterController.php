<?php

namespace App\Http\Controllers\Api\Seller\Auth;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\Auth\RegisterRequest;
use App\Http\Services\Seller\AuthService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    




    public function __construct(protected AuthService $authService){

        
    }


    /**
     * Register a new seller 
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request)  : JsonResponse{


        if(site_settings('seller_registration') == StatusEnum::false->status()) return api(['errors' => [translate('Seller Registration is currently off.')]])->fails(__('response.error'));

        return api([
            'access_token' => $this->authService->getAccessToken( DB::transaction(fn() =>  $this->authService->createSeller($request)))
        ])->success(__('response.success'));
    }


}
