<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BkashTokenizePaymentController extends Controller
{
    public function payment()
    {
        return redirect()->route('home');
    }


}
