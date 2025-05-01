<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deliveryman\DeliveryManResource;

use App\Models\DeliveryMan;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class ProfileController extends Controller
{
    protected ? DeliveryMan $deliveryman;

    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->deliveryman = auth()->guard('delivery_man:api')->user()?->load(['ratings','orders','refferedBy']);
            return $next($request);
        });
    }
  

    /**
     * Update profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request) : JsonResponse{

        $validator = Validator::make($request->all(),[
            'first_name'   => "required|max:191",
            'last_name'    => "nullable|string",
            'username'     => "required|unique:delivery_men,username,".$this->deliveryman->id,
            'email'        => "required|unique:delivery_men,email,".$this->deliveryman->id,
            'country_id'   => "required|exists:countries,id",
            'latitude'     => "required",
            'longitude'    => "required",
            'address'      => "required",
            'image'        => [ new FileExtentionCheckRule(file_format())],
        ]);
        
        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        

        $this->deliveryman->first_name = $request->first_name;
        $this->deliveryman->last_name = $request->last_name;
        $this->deliveryman->email  = $request->email;
        $this->deliveryman->username = $request->username;
        $this->deliveryman->country_id = $request->country_id;
        $this->deliveryman->address = [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'address'   => $request->address,
        ];

        if($request->hasFile('image')){
            try{
                $this->deliveryman->image = store_file($request->image, file_path()['profile']['delivery_man']['path'],null,$this->deliveryman->image);
            }catch (\Exception $exp){
          
            }
        }

        $this->deliveryman->save();

        return api(
            [
                'message'                     => translate('Profile updated'),
                'deliveryman'                  => new DeliveryManResource($this->deliveryman),
            ])->success(__('response.success'));

    }




    /**
     * Update password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordUpdate(Request $request) : JsonResponse {

 
     
        $validator = Validator::make($request->all(),[
            'current_password' => 'required',
            'password'         => 'required|min:5|confirmed',
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        if (!Hash::check($request->input("current_password"), $this->deliveryman->password)) return api(
                            ['errors'=> [translate("Current password does not matach")]])->fails(__('response.fail'));
        
        $this->deliveryman->password = Hash::make($request->input("password"));
        $this->deliveryman->save();
        
        return api(
            [
                'message' => translate('Password updated'),
            ])->success(__('response.success'));;


    }






    /**
     * Summary of referralCodeUpdate
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function referralCodeUpdate(Request $request) : JsonResponse {


        if(site_settings('deliveryman_referral_system') != StatusEnum::true->status()) return api(['errors'=> [translate("This module is not available")]])
        ->fails(__('response.fail'));  

        $validator = Validator::make($request->all(),[
            'referral_code' => 'required|digits:6|unique:delivery_men,referral_code',
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $this->deliveryman->referral_code = $request->input("referral_code");
        $this->deliveryman->save();
        
        return api(
            [
                'message' => translate('Referral code updated'),
            ])->success(__('response.success'));;

    }



    /**
     * Deliveryman logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout (Request $request) : JsonResponse {

        $this->deliveryman->fcm_token = null;
        $this->deliveryman->save(); 
        $this->deliveryman->tokens()->delete();
        return api(['message' => translate('You have been successfully logged out!')])->success(__('response.success'));

    }

}
