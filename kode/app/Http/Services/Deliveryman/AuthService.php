<?php

namespace App\Http\Services\Deliveryman;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\RewardPointLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService extends Controller
{


  
    /**
     * Get deliveryman via phone
     *
     * @param string|integer $phoneCode
     * @param string|integer $phone
     * @return DeliveryMan|null
     */
    public function getActiveDeliverymanByPhone(string|int $phoneCode , string|int $phone) :  ?DeliveryMan{
        return DeliveryMan::active()
                 ->where('phone',$phone)
                 ->where('phone_code',$phoneCode)
                 ->first();
    }


    /**
     * Create access token
     *
     * @param DeliveryMan $seller
     * @return string
     */
    public function getAccessToken(DeliveryMan $deliveryMan) : string {

        return  $deliveryMan->createToken(TokenKey::DELIVERY_MAN_AUTH_TOKEN->value,['role:'.TokenKey::DELIVERY_MAN_TOKEN_ABILITIES->value])
                         ->plainTextToken;
    }

    


    /**
     * Summary of register
     * @param \Illuminate\Http\Request $request
     * @return DeliveryMan
     */
    public function register(Request $request) : ?DeliveryMan{



        $deliveryman = new DeliveryMan();

        $deliveryman->first_name = $request->input('first_name');
        $deliveryman->last_name = $request->input("last_name");
        $deliveryman->email  = $request->input('email');
        $deliveryman->username = $request->input("username");
        $deliveryman->phone = $request->input("phone");
        $deliveryman->password = Hash::make($request->input('password'));
        $deliveryman->phone_code = $request->input('phone_code');
        $deliveryman->country_id = $request->input('country_id');
        $deliveryman->address = [
                'latitude'  => $request->input('latitude')  ?? null,
                'longitude' => $request->input("longitude") ?? null,
                'address'   => $request->input('address'),
        ];



        if(site_settings('deliveryman_referral_system') == StatusEnum::true->status()){

               $referralCode = $request->input('referral_code');

               if($referralCode){


                    $refferedBy = DeliveryMan::where('referral_code',$referralCode)
                                                                ->active()
                                                                ->first();

                    if($refferedBy){

                        $deliveryman->referral_id  =  $refferedBy->id;

                        #MAKE POINT LOG
                        $point = site_settings('deliveryman_referral_reward_point',0);
                        $details =  $point . translate(' added as a referral bonus ') . $deliveryman->username.translate(' registered using your referral code ');
                        $pointLog                     = new RewardPointLog();
                        $pointLog->delivery_man_id    = $refferedBy->id;
                        $pointLog->post_point         = $refferedBy->point;
                        $pointLog->point              = $point;
                        $pointLog->details            = $details;
                        $pointLog->save();

                        $refferedBy->point+=$point;
                        $refferedBy->save();

                    }


               }

           

        }

        $deliveryman->save();

        return $deliveryman;

    }

}