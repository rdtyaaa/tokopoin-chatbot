<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Settings\CacheKey;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:view_settings'])->only('index');
        $this->middleware(['permissions:update_settings'])->only('store','update','delete','default');
    }


    /**
     * Get all currencies
     *
     * @return View
     */
    public function index() :View
    {
        $title      = translate('Manage currencies');
        $currencies = Currency::latest()->paginate(site_settings('pagination_number',10));

        return view('admin.setting.currency', compact('title', 'currencies'));
    }


    /**
     * Set Default currency
     *
     * @param int | string $id
     * @return RedirectResponse
     */
    public function default(int | string $id) :RedirectResponse
    {

        Currency::where('id','!=',$id)->update([
            'default'=> 2
        ]);
        Currency::where('id',$id)->update([
            'default' => 1,
            'status'  => 1,
        ]);

        Cache::forget(CacheKey::DEFAULT_CURRENCY->value);
        return back()->with('success',translate('Default Status Updated Successfully'));
    }


    /**
     * Store a new currency
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request) :RedirectResponse
    {
        $data = $this->validate($request, [
            'name'    => 'required|max:50|unique:currencies,name',
            'symbol'  => 'required|max:10',
            'rate'    => 'required|numeric|gt:0',
        ]);
        Currency::create($data);
        return back()->with('success',translate('Currency has been created'));
    }


    /**
     * Update a currency
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request) :RedirectResponse
    {
        $data = $this->validate($request, [
            'name'    => 'required|max:50|unique:currencies,name,'.$request->id,
            'symbol'  => 'required|max:10',
            'rate'    => 'required|numeric|gt:0'
        ]);
        $currency = Currency::where('id',$request->id)->firstOrfail();
        $currency->update($data);
        return back()->with('success',translate('Currency has been updated'));
    }


    /**
     * Update admin status
     *
     * @param Request $request
     * @return string
     */
    public function statusUpdate(Request $request) :string {

        $request->validate([
            'data.id'=>'required|exists:currencies,id'
        ],[
            'data.id.required'=>translate('The id field is required')
        ]);

        $response['status']  = false;
        $response['message'] = translate('You can not inacitve system current currency');

        $currency              = Currency::where('id','!=',@session()->get('web_currency')->id)
                                         ->where('id',$request->data['id'])
                                         ->first();
        if(!$currency){
            return json_encode([$response]);
        }
        $response              = update_status($currency->id,'Currency',$request->data['status']);
        $response['reload']    = true;
        return json_encode([$response]);
    }



    /**
     * Delete a specific currency
     *
     * @param Request $request
     * @return RedirectResponse
     */
     public function delete(Request $request) :RedirectResponse{

        $currency = Currency::with(['withdraw','paymentMethods'])
                               ->where('id','!=',@session()->get('web_currency')->id)
                               ->where('id',$request->id)->firstOrfail();


        if(count($currency->withdraw) > 0 || count($currency->paymentMethods) > 0){
            return back()->with('error',translate('Please delete all payment and withdraw method under this currency then try agian !!'));
        }

        $currency->delete();

        return back()->with('success',translate('Currency Deleted Suceesfully'));


     }

}
