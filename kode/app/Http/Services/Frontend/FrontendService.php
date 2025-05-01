<?php

namespace App\Http\Services\Frontend;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class FrontendService extends Controller
{


    //get all active lang

    public function language(){
        return Language::active()->get();
    }
}