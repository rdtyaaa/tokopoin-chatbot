<?php

namespace App\Http\Controllers\Api\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Models\Seller;
use App\Models\SellerPasswordReset;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class PasswordResetController extends Controller
{
    




    /**
     * Verify email for password reset
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmail(Request $request) : JsonResponse {


        $validator = Validator::make($request->all(),[
            'email'                        => 'required|exists:sellers,email',
        ]);

        if ($validator->fails()){
            return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        }


        $seller = Seller::where('email',$request->input('email'))->first();


        if(!$seller) return api(['errors'=> [translate("Seller not found")]])
                               ->fails(__('response.fail'));


        SellerPasswordReset::where('email', $seller->email)->delete();
        $sellerPasswordReset =  SellerPasswordReset::create([
            'email'       => $seller->email,
            'verify_code' => random_number(),
        ]);

        $mailCode = [
            'code' => $sellerPasswordReset->verify_code, 
            'time' => $sellerPasswordReset->created_at,
        ];

        SendMailJob::dispatch($seller,'SELLER_PASSWORD_RESET',$mailCode);

        return api(
            [
                'message' => translate('Check your email password reset code sent successfully'),
  
            ])->success(__('response.success'));



    }



    /**
     * Verify otp code 
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyCode(Request $request) :JsonResponse {

        $validator = Validator::make($request->all(),[
            'email'                        => 'required|exists:sellers,email',
            'code'                         => 'required|exists:seller_password_resets,verify_code',
        ]);


        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


        $sellerResetToken = SellerPasswordReset::where('email', $request->input('email'))
                                    ->where('verify_code', $request->input('code'))
                                    ->first();


        if(!$sellerResetToken) return api(['errors'=> [translate("Invalid OTP code")]])
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
            'email'                        => 'required|exists:sellers,email',
            'code'                         => 'required|exists:seller_password_resets,verify_code',
            'password'                     => 'required|confirmed|min:6',
        ]);


        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));


    	$email = $request->input('email');
    	$sellerResetToken = SellerPasswordReset::where('email', $email)
                             ->where('verify_code', $request->input('code'))
                             ->first();
    	if(!$sellerResetToken) return api(['errors'=> [translate("Invalid OTP code")]])
        ->fails(__('response.fail'));

    	$seller           = Seller::where('email', $email)->first();


    	if(!$seller) return api(['errors'=> [translate("Invalid seller email")]])
        ->fails(__('response.fail'));

    	$seller->password = Hash::make($request->input('password'));
    	$seller->save();

		SendMailJob::dispatch($seller,'SELLER_PASSWORD_RESET_CONFIRM',[
            'time' => Carbon::now(),
        ]);
        $sellerResetToken->delete();



        return api(
            [
                'message' => translate('Password updated'),
  
            ])->success(__('response.success'));


    }
}
