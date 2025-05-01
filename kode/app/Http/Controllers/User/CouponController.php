<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{




    /**
     * Apply coupon
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function applyCoupon(Request $request) : JsonResponse{


        $request->validate([
            'code'     => 'required',
            'subtotal' => 'required|numeric|gt:0',
        ],[
            'code.required' => 'Coupon code is required'
        ]);


        if(session()->has('coupon') && session('coupon')['code'] == $request->input('code')) return response()->json(['error'=>'Coupon has applied already']);


        $coupon = Coupon::where('code', $request->input('code'))
                                        ->valid()
                                        ->first();



 
        if(!$coupon) return response()->json(['error'=> translate('This coupon doesn\'t exist')]);

        $amount = round(($coupon->discount(($request->subtotal))));

        if((int)$amount == 0)   return response()->json(['error'=> translate('Sorry, your order total doesn\'t meet the requirements for this coupon')]);

        $response = [
            'success' => translate('Coupon has applied successfully'),
            'code'    => $coupon->code,
            'amount'  => $amount,
        ]; 
        session()->put('coupon', ['code'=>$coupon->code,'amount' => $amount]);

        return response()->json($response);
    }
}