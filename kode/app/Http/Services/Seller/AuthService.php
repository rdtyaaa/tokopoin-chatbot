<?php

namespace App\Http\Services\Seller;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService extends Controller
{




    /**
     * Create a seller & its shop
     *
     * @param Request $request
     * @return Seller
     */
    public function createSeller(Request $request) :Seller {


         $seller  = Seller::create([
                            'username' => $request->input("username"),
                            'email'    => $request->input("email"),
                            'status'   => StatusEnum::true->status(),
                            'password' => Hash::make($request->input('password')),
                    ]);


        $seller->sellerShop()->create([
            'seller_id' => $seller->id,
        ]);

        return     $seller;

    }


    /**
     * Get seller via username
     *
     * @param string $username
     * @return Seller|null
     */
    public function getActiveSellerByUsername(string $username) :  ? Seller {
        return Seller::active()->where('username',$username)->first();
    }




    /**
     * Create user access token
     *
     * @param Seller $seller
     * @return string
     */
    public function getAccessToken(Seller $seller) : string {

        return  $seller->createToken(TokenKey::SELLER_AUTH_TOKEN->value,['role:'.TokenKey::SELLER_TOKEN_ABILITIES->value])
                         ->plainTextToken;
    }

}