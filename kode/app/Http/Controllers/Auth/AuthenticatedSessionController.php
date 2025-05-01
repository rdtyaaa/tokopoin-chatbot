<?php

namespace App\Http\Controllers\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Frontend\ProductService;
use App\Http\Utility\SendMail;
use App\Http\Utility\SendSMS;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
class AuthenticatedSessionController extends Controller
{

    public $productService;
    public function __construct()
    {
        $this->productService = new ProductService();
    }


   /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        $title = translate('Login');
        return view('auth.login', compact('title'));
    }


    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {


        $field = preg_match('/^[0-9]+$/', request()->input('email'))
                         ? 'phone'
                         : 'email';

        if((site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()  &&  $field != 'email') && site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::false->status()){
            return back()->with('error',translate('Please enter your email'));
        }
        if(
            (site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()
             &&  $field != 'phone')
             && (site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::false->status())){
            return back()->with('error',translate('Please enter your phone'));
        }

        $credentials  = [$field => request()->input('email') ,'password' => request()->input('password') ];


        if(@site_settings('login_with_password',StatusEnum::true->status()) == StatusEnum::true->status() ){
            if(!Auth::attempt($credentials,true)){
                return back()->with('error',translate('Invalid credential'));
             }
            $user =  Auth::guard('web')->user();
        }else{
            $user = User::where(function ($query) use ($request) {
                $query->where('email', $request->input('email'))
                      ->orWhere('phone', $request->input('email'));
            })->first();
        }

        if(!$user){
            return back()->with('error',translate('Invalid credential'));
        }


        if(site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()
                || site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::true->status() ){
                    Auth::guard('web')->logout();
                    return $this->sendOTP( $user );
        }



        Auth::guard('web')->login($user);
        $this->productService->updateCart($user);

        $requestedRoute =  RouteServiceProvider::HOME;

        if(session()->has('request_route')){
            $requestedRoute = route(session()->has('request_route'));
            session()->forget('request_route');
        }

        return redirect()->intended( $requestedRoute);
    }


    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function otpVerificationView()
    {
        $title = translate('OTP Verification');
        return view('auth.otp_verification', compact('title'));
    }


    public function verifyOTP(Request $request){

        $request->validate([
            'otp' => 'required',
        ],[
            'otp.required' => 'The OTP filed is required'
        ]);
        $user = User::where('otp_code',$request->input('otp'))->first();

        if(!$user){
            return redirect()->back()->with('error','Invalid OTP code');
        }
        Auth::guard('web')->login($user);

        $this->productService->updateCart($user);
        return redirect()->intended(RouteServiceProvider::HOME);

    }



    public function sendOTP(User $user ){

        $user->otp_code =  random_number();
        $user->save();
        $templateCode = [
            'otp_code' => $user->otp_code,
            'time'     => Carbon::now(),
        ];
        $message = translate('Verification OTP  sent successfully to user, please check your  email or phone');

        if(site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()){
            if($user->phone){

                SendSmsJob::dispatch($user,'otp_verification',$templateCode);
                $message = translate('Verification OTP  sent successfully to your phone');

            }
        }
        if(site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()){

            $response = SendMailJob::dispatch($user,'otp_verification',$templateCode);


            $message = translate('Verification OTP  sent successfully to your email');

        }

        return redirect()->route('otp.verification.view')->with('success',$message);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $webCurrency = request()->session()->get('web_currency');

        Auth::guard('web')->logout();

        if ($webCurrency !== null) {
            request()->session()->put('web_currency', $webCurrency);
        }
        return redirect('/');
    }
}
