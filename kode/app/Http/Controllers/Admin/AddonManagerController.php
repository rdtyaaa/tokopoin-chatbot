<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Traits\SystemUpdateManager;
class AddonManagerController extends Controller
{

    use SystemUpdateManager;
    public function __construct(){

        $this->middleware(['permissions:update_settings'])->only('list','store');
    }




    /**
     * Return addon manager
     *
     * @return View
     */
    public function manager() : View {


        return view('admin.addon_manager',[
            "title" => translate("Addon manager")
        ]);
    }



    /**
     * Store a addon
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request) : array {

        $request->validate([
            'updateFile' => ['required', 'mimes:zip'],
            'purchase_key' => ['required'],
        ]);

        return $this->_addon( $request);

    }












}
