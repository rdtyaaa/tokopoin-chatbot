<?php

namespace App\Http\Controllers\Api\Delivery\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Deliveryman\AuthService;
use App\Jobs\SendSmsJob;
use App\Models\DeliverymanPasswordReset;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class PasswordResetController extends Controller
{
    


    public function __construct(protected AuthService $authService){

        
    }


    /**
     * Verify phone for password reset
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPhone(Request $request) : JsonResponse {

        $validator = Validator::make($request->all(),[
            'phone'      => ['required','exists:delivery_men,phone'],
            'phone_code' => ['required','exists:delivery_men,phone_code']
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

         $deliveryman = $this->authService->getActiveDeliverymanByPhone($request->input("phone_code"),$request->input("phone"));


        if(!$deliveryman) return api(['errors'=> [translate("Invalid user")]])
                               ->fails(__('response.fail'));


        DeliverymanPasswordReset::where('identifier', $deliveryman->phone)->delete();

        $resetPassword =  DeliverymanPasswordReset::create([
            'identifier'       =>  $deliveryman->phone,
            'token'            => random_number(),
        ]);

        $code = [
            'code' => $resetPassword->verify_code, 
            'time' => $resetPassword->token,
        ];

        SendSmsJob::dispatch($deliveryman,'PASSWORD_RESET',$code);
        return api(
            [
                'message' => translate('Check your phone password reset code sent successfully'),
  
            ])->success(__('response.success'));

    }



    /**
     * Verify OTP code 
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyCode(Request $request) :JsonResponse {

        $validator = Validator::make($request->all(),[
            'phone'      => ['required','exists:delivery_men,phone'],
            'phone_code' => ['required','exists:delivery_men,phone_code'],
            'code'       => 'required|exists:deliveryman_password_resets,token'
        ]);


        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


        $token =     DeliverymanPasswordReset::where('identifier', $request->input('phone'))
                                        ->where('token', $request->input('code'))             
                                        ->first();
        

        if(!$token) return api(['errors'=> [translate("Invalid OTP code")]])
        ->fails(__('response.fail'));

        return api(
            [
                'message' => translate('OTP verification successuly completed'),
  
            ])->success(__('response.success'));

    }



    /**
     * Update password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordReset(Request $request) : JsonResponse {

        $validator = Validator::make($request->all(),[
            'phone'      => ['required','exists:delivery_men,phone'],
            'phone_code' => ['required','exists:delivery_men,phone_code'],
            'code'       => 'required|exists:deliveryman_password_resets,token',
            'password'                     => 'required|confirmed|min:6',
        ]);


        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


        $token =     DeliverymanPasswordReset::where('identifier', $request->input('phone'))
                                                ->where('token', $request->input('code'))             
                                                ->first();

                             
    	if(!$token) return api(['errors'=> [translate("Invalid OTP code")]])
        ->fails(__('response.fail'));

        $deliveryman = $this->authService->getActiveDeliverymanByPhone($request->input("phone_code"),$request->input("phone"));


    	if(!$deliveryman) return api(['errors'=> [translate("Invalid User")]])
        ->fails(__('response.fail'));

    	$deliveryman->password = Hash::make($request->input('password'));
    	$deliveryman->save();
        $token->delete();



        return api(
            [
                'message' => translate('Password updated'),
  
            ])->success(__('response.success'));


    }
}
