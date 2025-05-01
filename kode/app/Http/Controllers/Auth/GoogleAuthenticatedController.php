<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;


class GoogleAuthenticatedController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/');
        }
   
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            Auth::login($existingUser);
            return redirect(RouteServiceProvider::HOME);
        } else {
            $newUser  = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->google_id = $user->id;
            $newUser->save();
            Auth::login($newUser);
            return redirect(RouteServiceProvider::HOME);
        }
        return redirect()->route('home');
    }
}
