<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Enums\StatusEnum;
use App\Models\Admin;

use App\Traits\InstallerManager;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InstallerController extends Controller
{

    use InstallerManager;

    public function __construct(){

        $this->middleware(function ($request, $next) {
            if($this->is_installed()
            && !$request->routeIs('install.setup.finished')
            && !$request->routeIs('invalid.puchase')
            && !$request->routeIs('verify.puchase')){
                return redirect()->route('home')->with('success',trans('default.already_installed'));
            }
            return $next($request);
        });

    }

    /**
     * Installer init
     *
     * @return View
     */
    public function init() :View
    {
        $this->_registerDomain();
        return view('install.init',[
            'title' => 'Install'
        ]);
    }


    /**
     * Requirments and permission verifications
     *
     * @return View |RedirectResponse
     */
    public function requirementVerification() : View |RedirectResponse
    {

        if (Hash::check(base64_decode('cmVxdWlyZW1lbnRz'), request()->input('verify_token'))) {

            return view('install.requirements',[
                'title' => 'Requirments',
                'requirements'    => $this->checkRequirements(
                    config('installer.requirements')),
                "phpSupportInfo"  => $this->checkPHPversion(Arr::get(config("installer.core"),"minPhpVersion")),
                'permissions'     => $this->permissionsCheck(config('installer.permissions'))
            ]);
        }

        return redirect()->route('install.init')->with('error','Invalid verification token');
    }


    /**
     * Envato verification view
     *
     * @return View |RedirectResponse
     */
    public function envatoVerification() :View |RedirectResponse
    {

        if(session()->get('system_requirments')){

            if (Hash::check(base64_decode('ZW52YXRvX3ZlcmlmaWNhdGlvbg=='), request()->input('verify_token'))) {
                return view('install.envato_verification',[
                    'title' => 'Envato Verification'
                ]);
            }
            return redirect()->route('install.init')->with('error','Invalid verification token');
        }

        return redirect()->back()->with('error','Server requirements not met. Ensure all essential Extension and file permissions are enabled to ensure proper functionality');


    }


    /**
     * Envato verification
     *
     * @param Request $request
     * @return View |RedirectResponse
     */
    public function codeVerification(Request $request) :View |RedirectResponse
    {
        $request->validate([
            base64_decode('cHVyY2hhc2VfY29kZQ==') => "required",
            base64_decode('dXNlcm5hbWU=')         => "required"
        ],[
            base64_decode('cHVyY2hhc2VfY29kZQ==').".required" => "Code is required",
            base64_decode('dXNlcm5hbWU=').".required"         => "Username is required",
        ]);

        if($this->_envatoVerification($request)){
            session()->put( base64_decode('cHVyY2hhc2VfY29kZQ=='), $request->input(base64_decode('cHVyY2hhc2VfY29kZQ==')));
            session()->put( base64_decode('dXNlcm5hbWU='), $request->input(base64_decode('dXNlcm5hbWU=')));
            return redirect()->route('install.db.setup',['verify_token' => bcrypt(base64_decode('ZGJzZXR1cF8='))]);
        }

        return redirect()->back()->with('error','Invalid verification code');
    }



    /**
     * Database configuration view
     *
     * @return View |RedirectResponse
     */
    public function dbSetup() :View |RedirectResponse
    {

        if(session()->get('system_requirments')){
            if (Hash::check(base64_decode('ZGJzZXR1cF8='), request()->input('verify_token'))) {
                return view('install.db_setup',[
                    'title' => 'Database Setup'
                ]);
            }
            return redirect()->route('install.init')->with('error','Invalid verification token');

        }
        return redirect()->back()->with('error','Server requirements not met. Ensure all essential Extension and file permissions are enabled to ensure proper functionality');

    }


    /**
     * Database setup
     *
     * @param Request $request
     * @return View |RedirectResponse
     */
    public function dbStore(Request $request) :View |RedirectResponse
    {

        try {

            $message = "Invalid database info. Kindly check your connection details and try again";
            $request->validate([
                'db_host'     => "required",
                'db_port'     => "required",
                'db_database' => "required",
                'db_username' => "required" ,

            ]);

            if($this->_chekcDbConnection( $request)){
                if($this->_envConfig($request)){
                    return redirect()->route('install.db.import',['verify_token' => bcrypt(base64_decode('ZGJfaW1wb3J0X3ZpZXc='))]);
                }
                $message = "Please empty your database then try again";
            }
            return back()->with("error", $message);


        } catch (\Exception $ex) {

            return redirect()->back()->with('error',strip_tags($ex->getMessage()));
        }

    }



    /**
     * Database Import view
     *
     * @param Request $request
     * @return View |RedirectResponse
     */
    public function dbImport(Request $request) :View |RedirectResponse
    {
        if (Hash::check(base64_decode('ZGJfaW1wb3J0X3ZpZXc='), request()->input('verify_token'))) {
            return view('install.db_import',[
                'title' => 'Import Database'
            ]);
        }
        return redirect()->route('install.init')->with('error','Invalid verification token');

    }


    /**
     * Database Import Store
     *
     * @param Request $request
     * @return View |RedirectResponse
     */
    public function dbImportStore() :View |RedirectResponse
    {
        if (Hash::check(base64_decode('ZGJfaW1wb3J0'), request()->input('verify_token'))) {

            try {

                $is_force = request()->input('force',false);
                if(!$is_force && !$this->_isDbEmpty()) {
                    return redirect()->back()
                           ->with('error','Please Empty Your database first!!');
                }

                $this->_dbMigrate($is_force);
                $this->_systemInstalled();
                return redirect()
                             ->route('install.setup.finished',['verify_token' => bcrypt(base64_decode('c2V0dXBfY29tcGxldGVk'))]);

            } catch (\Exception $ex) {
                return redirect()->back()
                           ->with('error',strip_tags($ex->getMessage()));
            }

        }
        return redirect()->route('install.init')->with('error','Invalid verification token');

    }






    /**
     * Installtion Finished
     *
     * @param Request $request
     * @return View |RedirectResponse
     */
    public function setupFinished(Request $request) :View |RedirectResponse
    {

        if (Hash::check(base64_decode('c2V0dXBfY29tcGxldGVk'), request()->input('verify_token'))) {
            $admin =  Admin::where('user_name' ,'admin')->first();
            optimize_clear();
            return view('install.setup_finished',[
                'admin' => $admin,
                'title' => 'Installed',
            ]);
        }
        return redirect()->route('install.init')->with('error','Invalid verification token');
    }


    /**
     * Invalid user
     *
     * @return View |RedirectResponse
     */
    public function invalidPuchase() :View |RedirectResponse
    {
        if(!$this->_isPurchased()){
            return view('install.invalid',[
                'title' => 'Invalid Software License',
                'note'  => 'Please Verify Yourself',
            ]);
        }
        return redirect()->route("home")->with('success','Your system is already verified');

    }


    /**
     * Verify purchase
     *
     * @param Request $request
     * @return View |RedirectResponse
     */
    public function verifyPuchase(Request $request) :View |RedirectResponse
    {

        $request->validate([
            base64_decode('cHVyY2hhc2VfY29kZQ==') => "required",
            base64_decode('dXNlcm5hbWU=')         => "required"
        ],[
            base64_decode('cHVyY2hhc2VfY29kZQ==').".required" => "Code is required",
            base64_decode('dXNlcm5hbWU=').".required"         => "Username is required",
        ]);

        if($this->_registerDomain() && $this->_validatePurchaseKey($request->input(base64_decode('cHVyY2hhc2VfY29kZQ==')))){


            $newPurchaseKey        = $request->input(base64_decode('cHVyY2hhc2VfY29kZQ=='));
            $newEnvatoUsername     =  $request->input( base64_decode('dXNlcm5hbWU='));
            update_env('PURCHASE_KEY',$newPurchaseKey);
            update_env('ENVATO_USERNAME',$newEnvatoUsername);
            optimize_clear();
            $this->_systemInstalled($newPurchaseKey,$newEnvatoUsername);
            return redirect()->route("admin.dashboard")->with("success","Verified Successfully");
        }

        return redirect()->back()->with("error","Invalid Purchase key");


    }


}
