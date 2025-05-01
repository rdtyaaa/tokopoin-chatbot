<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('admin.guest')->except('logout');
    }



    /**
     * Get admin login view
     *
     * @return View
     */
    public function showLogin() :View {

        $title = translate('Admin Login');
        return view('admin.auth.login', compact('title'));
    }



    /**
     * Authentication process for admin guard
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request) : RedirectResponse{

        $credentials = $request->validate([
            'user_name' => ['required'],
            'password' => ['required'],
        ]);

        $remember_me = $request->has('remember_me') ? true : false;

        if (Auth::guard('admin')->attempt($credentials, $remember_me )) {
            if(auth_user()->status  == StatusEnum::false->status()){
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with("error",'Your account is banned by superadmin');
            }
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }




    /**
     * Logout a auth user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request) : RedirectResponse {

        Auth::guard('admin')->logout();

        return redirect('/admin');
    }
}
