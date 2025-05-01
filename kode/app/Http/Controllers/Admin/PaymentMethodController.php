<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class PaymentMethodController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:view_method'])->only('index');
        $this->middleware(['permissions:update_method'])->only('edit',"update");
    }


    /**
     * Get all payment methods
     *
     * @return View
     */
    public function index() :View {

        $title          = translate('Payment methods');

        $paymentMethods = PaymentMethod::search()
                                    ->with(['currency'])
                                    ->when(request()->routeIs('admin.gateway.payment.manual.*'),
                                            fn(Builder $query) => $query->manual(),
                                            fn(Builder $query) => $query->automatic())
                                    ->latest()
                                    ->get();

        return view('admin.payment.index', compact('title', 'paymentMethods'));
    }




    /**
     * Create a payment method
     *
     * @return View
     */
    public function create() :View {

        $title         = translate('Payment method create');
        $currencies    = Currency::latest()->get();
        return view('admin.payment.create', compact('title', 'currencies'));
    }





    /**
     * Store a manual payment method
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request) :RedirectResponse {


        $this->validate($request, [
            'name'           => 'required|unique:payment_methods,name',
            'currency_id'    => 'required|exists:currencies,id',
            'percent_charge' => 'required|numeric|gt:-1',
            'rate'           => 'required|numeric|gt:-1',
            'image'          => 'nullable|image|mimes:jpg,png,jpeg',
        ]);



        $paymentMethod                 = new PaymentMethod();
        $paymentMethod->currency_id    = $request->input('currency_id');
        $paymentMethod->name           = $request->input('name');
        $paymentMethod->percent_charge = $request->input('percent_charge');
        $paymentMethod->rate           = $request->input('rate');
        $paymentMethod->status         = PaymentMethod::ACTIVE;
        $paymentMethod->type           = PaymentMethod::MANUAL;

        $paymentMethod->payment_parameter = collect($request->input('data_name',[]))->map(function(string $value , int $key) use($request){

            $types       = $request->input('type',[]);
            $required    = $request->input('required',[]);
            return [
                "name"        => t2k($value),
                "type"        => Arr::get($types , $key ,'text'),
                "is_required" => Arr::get($required , $key ,'required') == 'required' ? true : false,
            ];

        });

        if($request->hasFile('image')){

            try {
                $paymentMethod->image = store_file( file      : $request->file("image"),
                                                    location   : file_path()['payment_method']['path'],
                                                    removefile : $paymentMethod->image ?: null);
                }catch (\Exception $exp) {

            }
        }
        $paymentMethod->save();

        return back()->with('success',translate('Payment method created'));



    }


    /**
     * Edit a payment method
     *
     * @param string $slug
     * @param integer $id
     * @return View
     */
    public function edit(string $slug,int $id) :View {

        $title         = translate('Payment method update');
        $paymentMethod = PaymentMethod::findOrFail($id);
        $currencies    = Currency::latest()->get();
        return view('admin.payment.edit', compact('title', 'paymentMethod', 'currencies'));
    }


    /**
     * Update a payment method
     *
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse
     */
    public function update(Request $request,int  $id) :RedirectResponse {


        $paymentMethod                 = PaymentMethod::findOrFail($id);

        $this->validate($request, [
            'name'           => ['nullable',Rule::requiredIf( fn() =>  $paymentMethod->type == PaymentMethod::MANUAL) ,'unique:payment_methods,name,'.$paymentMethod->id],
            'status'         => 'required|in:1,2',
            'currency_id'    => 'required|exists:currencies,id',
            'percent_charge' => 'required|numeric|gt:-1',
            'rate'           => 'required|numeric|gt:-1',
            'image'          => 'nullable|image|mimes:jpg,png,jpeg',
        ]);

        $paymentMethod->currency_id    = $request->input('currency_id');
        $paymentMethod->percent_charge = $request->input('percent_charge');
        $paymentMethod->rate           = $request->input('rate');
        $paymentMethod->status         = $request->input('status');


        $parameter = [];

        switch ($paymentMethod->type) {

            case PaymentMethod::AUTOMATIC:
                foreach ($paymentMethod->payment_parameter as $key => $value) {
                    $parameter[$key] = $request->method[$key];
                }
                break;
            case PaymentMethod::MANUAL:
                $parameter =  collect($request->input('data_name',[]))->map(function(string $value , int $key) use($request){
                    $types       = $request->input('type',[]);
                    $required    = $request->input('required',[]);

                    return [
                        "name"        => t2k($value),
                        "type"        => Arr::get($types , $key ,'text'),
                        "is_required" => Arr::get($required , $key ,'required') == 'required' ? true : false,
                    ];

                });
                break;


        }


        $paymentMethod->payment_parameter = $parameter;

        if($request->hasFile('image')){

            try {
                $paymentMethod->image = store_file(file      : $request->file("image"),
                                                  location   : file_path()['payment_method']['path'],
                                                  removefile : $paymentMethod->image ?: null);
            }catch (\Exception $exp) {

            }
        }
        $paymentMethod->save();

        return back()->with('success',translate('Payment method has been updated'));
    }


    /**
     * delete a payment method
     *
     * @param integer $id
     * @return RedirectResponse
     */
    public function delete(int  $id) :RedirectResponse {

        $paymentMethod                 = PaymentMethod::manual()->findOrFail($id);

        $paymentMethod->delete();

        return back()->with('success',translate('Deleted successfully'));


    }


}
