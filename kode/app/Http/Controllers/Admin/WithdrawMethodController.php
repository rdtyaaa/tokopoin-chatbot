<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawMethodController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:view_method'])->only('index');
        $this->middleware(['permissions:update_method'])->only('edit',"update");
        $this->middleware(['permissions:create_method'])->only('create',"store");
        $this->middleware(['permissions:delete_method'])->only('edit',"update");

    }
    public function index() :View
    {
        $title           = translate('Manage withdraw method');
        $withdrawMethods = WithdrawMethod::latest()->with('currency')->paginate(site_settings('pagination_number',10));
        return view('admin.withdraw_method.index', compact('title', 'withdrawMethods'));
    }

    public function create() :View
    {
        $title       = translate('Add new withdraw method');
        $currencies  = Currency::latest()->select('id', 'name')->get();
        return view('admin.withdraw_method.create', compact('title', 'currencies'));
    }

    public function store(Request $request) :RedirectResponse
    {
        $request->validate([
            'name'           => 'required|max:60',
            'file'           => 'required|image|mimes:jpeg,jpg,png',
            'currency_id'    => 'required|exists:currencies,id',
            'rate'           => 'required|numeric|gt:0',
            'duration'       => 'required|integer',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'description'    => 'required|max:64000',
            'data_name.*'    => 'sometimes|required'
        ],[
            'data_name.*.required'=>'User data all field is required'
        ]);
        $userInformationData = [];
        if ($request->has('data_name')) {
            for ($i=0; $i<count($request->data_name); $i++){
                $array = [];
                $array['data_label'] = $request->data_name[$i];
                $array['data_name'] = strtolower(str_replace(' ', '_', $request->data_name[$i]));
                $array['type'] = $request->type[$i];
                $userInformationData[$array['data_name']] = $array;
            }
        }

        $filename = null;
        if ($request->hasFile('file')) {
            try {
                $filename = store_file($request->file, file_path()['withdraw']['path']);
            } catch (\Exception $exp) {

            }
        }
        WithdrawMethod::create([
            'name'             => $request->name,
            'image'            => $filename,
            'currency_id'      => $request->currency_id,
            'duration'         => $request->duration,
            'min_limit'        => $request->min_limit,
            'max_limit'        => $request->max_limit,
            'rate'             => $request->rate,
            'fixed_charge'     => $request->fixed_charge,
            'percent_charge'   => $request->percent_charge,
            'description'      => build_dom_document($request->description,'method_description'.rand(1,400)),
            'user_information' => $userInformationData,
            'status' => 1
        ]);

        return back()->with('success',translate('Withdraw method has been added.'));
    }

    public function edit(int $id) :View
    {
        $title          = translate('Update withdraw method');
        $withdrawMethod = WithdrawMethod::findOrFail($id);
        $currencies     = Currency::latest()->select('id', 'name')->get();
        return view('admin.withdraw_method.edit', compact('title', 'withdrawMethod', 'currencies'));
    }

    public function update(Request $request, int $id) :RedirectResponse
    {
        $request->validate([
            'name'            => 'required|max:60',
            'file'            => 'nullable|image|mimes:jpeg,jpg,png',
            'currency_id'     => 'required|exists:currencies,id',
            'rate'            => 'required|numeric|gt:0',
            'duration'        => 'required|integer',
            'min_limit'       => 'required|numeric|gt:0',
            'max_limit'       => 'required|numeric|gt:min_limit',
            'fixed_charge'    => 'required|numeric|gte:0',
            'percent_charge'  => 'required|numeric|between:0,100',
            'description'     => 'required|max:64000',
            'data_name.*'     => 'sometimes|required'
        ],[
            'data_name.*.required'=>'User data all field is required'
        ]);
        $userInformationData = [];
        if ($request->has('data_name')) {
            for ($i=0; $i<count($request->data_name); $i++){
                $array = [];
                $array['data_label'] = $request->data_name[$i];
                $array['data_name'] = strtolower(str_replace(' ', '_', $request->data_name[$i]));
                $array['type'] = $request->type[$i];
                $userInformationData[$array['data_name']] = $array;
            }
        }
        $withdrawMethod = WithdrawMethod::findOrFail($id);
        $filename      = $withdrawMethod->image;
        if ($request->hasFile('file')) {
            try {
                $filename = store_file($request->file, file_path()['withdraw']['path'],null,  $filename);
            } catch (\Exception $exp) {

            }
        }
        $withdrawMethod->update([
            'name'             => $request->name,
            'image'            => $filename,
            'currency_id'      => $request->currency_id,
            'duration'         => $request->duration,
            'rate'             => $request->rate,
            'min_limit'        => $request->min_limit,
            'max_limit'        => $request->max_limit,
            'fixed_charge'     => $request->fixed_charge,
            'percent_charge'   => $request->percent_charge,
            'description'      => build_dom_document($request->description,'method_description_edit'.rand(1,400)),

            'user_information' => $userInformationData,
            'status' => 1
        ]);

        return back()->with('success',translate('Withdraw method has been updated.'));
    }


    public function delete(Request $request) :RedirectResponse
    {
        $wiithDraweMethod = withdrawMethod::with(['withdarwLogs'])->where('id', $request->id)->firstOrfail();
        if(count( $wiithDraweMethod->withdarwLogs) > 0){
            return back()->with('error',translate('Withdraw Method Can Not be deleted,Because This Withdrow Method Has Withdrow Log'));
        }

        $wiithDraweMethod->delete();
        return back()->with('success',translate("Withdraw Method has been deleted"));


    }
}
