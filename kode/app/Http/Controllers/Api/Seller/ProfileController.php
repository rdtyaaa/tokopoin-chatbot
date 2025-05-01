<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\PasswordUpdateRequest;
use App\Http\Requests\Api\Seller\ProfileRequest;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Services\Seller\SellerService;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileController extends Controller
{
    protected ? Seller $seller;

    public function __construct(protected SellerService $sellerService){
        $this->middleware(function ($request, $next) {
            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);
            return $next($request);
        });
    }

    /**
     * Update seller profile
     *
     * @param ProfileRequest $request
     * @return JsonResponse
     */
    public function update(ProfileRequest $request) : JsonResponse{

        $response  = $this->sellerService->updateProfile($request ,$this->seller);

        return api(
            [
                'message' => translate('Profile updated'),
                'seller'                  => new SellerResource($this->seller),
            ])->success(__('response.success'));

    }




    /**
     *Update password
     *
     * @param PasswordUpdateRequest $request
     * @return JsonResponse
     */
    public function passwordUpdate(PasswordUpdateRequest $request) : JsonResponse {


        $response  = $this->sellerService->updatePassword($request ,$this->seller);


        switch ($response) {
            case true :
                return api(
                    [
                        'message' => translate('Password updated'),
                    ])->success(__('response.success'));
                break;
            
            default:
                    return api(
                        ['errors'=> [translate("Current password does not matach")]])->fails(__('response.fail'));
                break;
        }


    }


    /**
     * Seller logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout (Request $request) : JsonResponse {

        $this->seller->tokens()->delete();
        return api(['message' => translate('You have been successfully logged out!')])->success(__('response.success'));

    }

}
