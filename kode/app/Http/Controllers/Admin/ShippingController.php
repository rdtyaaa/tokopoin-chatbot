<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Settings\CacheKey;
use App\Enums\Settings\GlobalConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use App\Models\ShippingDelivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Enums\ShippingOption;
use App\Models\Country;
use App\Models\Setting;
use App\Models\Zone;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{

    public function __construct()
    {

        $this->middleware(['permissions:view_settings'])->only('method', 'shippingIndex','configuration');
        $this->middleware(['permissions:create_settings'])->only('methodStore', 'shippingUpdate', 'shippingCreate','shippingEdit', 'shippingStore','configurationStore');
        $this->middleware(['permissions:update_settings'])->only('methodUpdate', 'methodDelete', 'shippingDelete');
    }

    /**
     *  Get all shipping method
     *
     * @return View
     */
    public function method(): View
    {
        $title           = translate('Shipping method');
        $shippingMethods = ShippingMethod::when(request()->input('search'), function ($q) {
            return $q->where('name', 'like', '%' . request()->input('search') . '%');
        })->latest()->get();
        return view('admin.shipping.method', compact('title', 'shippingMethods'));
    }


    /**
     *  Create a new shipping method
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function methodStore(Request $request): RedirectResponse
    {

        $data = $this->validate($request, [
            'name'   => 'required|max:255',
            'status' => 'required|in:1,2'
        ]);
        $image = null;
        if ($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['shipping_method']['path']);
            } catch (\Exception $exp) {
            }
        }
        $data['image'] = $image;
        ShippingMethod::create($data);
        return back()->with('success', translate('Shipping method has been created'));
    }


    /**
     * Update Shipping method
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function methodUpdate(Request $request): RedirectResponse
    {
        $data = $this->validate($request, [
            'name'   => 'required|max:255',
            'status' => 'required|in:1,2'
        ]);
        $shipping = ShippingMethod::findOrFail($request->id);
        $image = $shipping->image;
        if ($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['shipping_method']['path'], null, $image);
            } catch (\Exception $exp) {
            }
        }
        $data['image'] = $image;
        $shipping->update($data);
        return back()->with('success', translate('Shipping method has been created'));
    }



    /**
     * Delete a shipping method
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function methodDelete(Request $request): RedirectResponse
    {
        $shippingMethod = shippingMethod::where('id', $request->id)->first();

        if (count($shippingMethod->shippingdelivery) > 0) {
            return back()->with('error', translate('Before delete shipping delivery and try again'));
        }

        $shippingMethod->delete();
        return back()->with('success', translate('Shiping methode has been deleted'));
    }

    /**
     * @return view
     */
    public function configuration():View
    {
        $title                 = translate('Shipping Configuration');
        $shippingOptions       = ShippingOption::toArray();

        return view('admin.shipping.configuration', compact('title', 'shippingOptions' ));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function configurationStore(Request $request):RedirectResponse
    {
        $validShippingOptions = array_keys(ShippingOption::toArray());


        $data = $request->validate([
            'shipping_option'           =>  ['required', Rule::in($validShippingOptions)],
            'standard_shipping_fee'     => 'required|numeric',

        ], [

            'shipping_option.required'              => translate('Shipping option is required'),
            'standard_shipping_fee.required'        => translate('Standard shipping fee  is required'),
        ]);



        Setting::updateOrInsert(
            ['key'    => 'shipping_configuration'],
            ['value'  =>  json_encode($data)]
        );

        Cache::forget(CacheKey::SITE_SETTINGS->value);

        optimize_clear();

        return back()->with('success', translate('Shipping configuration Added'));
    }


    /**
     * Get all shipping Delivary
     *
     * @return View
     */
    public function shippingIndex(): View
    {

        $title              = translate('Manage Shipping Delivery');
        $methods            = ShippingMethod::where('status', 1)->get();
        $shippingDeliverys  = ShippingDelivery::latest()->with('method')
            ->when(request()->input('search'), function ($q) {
                $searchBy = '%' . request()->input('search') . '%';
                return $q->where('name', 'like', $searchBy)
                    ->orWhereHas('method', function ($q) use ($searchBy) {
                        $q->where("name", 'like', $searchBy);
                    });
            })
            ->get();

        return view('admin.shipping.delivery', compact('title', 'shippingDeliverys', 'methods'));
    }



    /**
     * Undocumented function
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function shippingStore(Request $request): RedirectResponse
    {

        $data = $this->validate($request, [
            'name'          => 'required',
            'duration'      => 'required',
            'description'   => 'required',
            'status'        => 'required|in:1,0',
            'free_shipping' => 'required|in:1,0',
            'shipping_type' => [Rule::requiredIf($request->input('free_shipping') == 0),Rule::in(['price_wise','weight_wise'])],
            'image'         => ['required','image',new FileExtentionCheckRule(file_format())],
        ]);



        if ($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['shipping_method']['path']);
            } catch (\Exception $exp) {
            }
        }
        $price_configuration = [];

        if($request->input('free_shipping') == 0){

            $key = $request->input('shipping_type') == "price_wise"
                                     ? 'price_base_zone_wise_price'
                                     : 'weight_base_zone_wise_price';
            $zoneWisePrices = $request->input($key);

            if(!$zoneWisePrices) return back()->with('error','Please create a zone first..');

            foreach ($zoneWisePrices as $zone_id => $costs) {
                foreach ($costs as $index => $cost) {

                    $lte =  @$request[$request->input('shipping_type')]["less_than_eq"][$index] ?? 0;
                    $gt  =  @$request[$request->input('shipping_type')]["greater_than"][$index] ?? 0;

                    if(!is_numeric(  $lte ) || !is_numeric(  $gt ) ) return back()->with('error',translate('Price must be numeric value'));
                    if( $lte  < 0 ||   $gt  < 0) return back()->with('error',translate('Price cannot be less than 0'));
                    if($gt > $lte) return back()->with('error',translate('Less than equal price must be higer than greater than prices'));

                    $price_configuration[] = [
                        "zone_id" => $zone_id,
                        "greater_than" => $gt,
                        "less_than_eq" => $lte,
                        "cost" => $cost
                    ];
                }
            }


        }

        $shipping = new ShippingDelivery();
        $shipping->name = $request->input("name");
        $shipping->duration = $request->input("duration");
        $shipping->description = $request->input("description");
        $shipping->free_shipping	 = $request->input("free_shipping");
        $shipping->shipping_type	 = $request->input("shipping_type");
        $shipping->status	 = $request->input("status");
        $shipping->image               = @$image;
        $shipping->price_configuration = $price_configuration;
        $shipping->save();

        return back()->with('success', translate('Shipping delivery has been store'));
    }


        /**
     * Update Shipping Delivery
     *
     * @param Request $request
     * @param  int | string $id $id
     * @return RedirectResponse
     */
    public function shippingUpdate(Request $request, int | string $id): RedirectResponse
    {


        $data = $this->validate($request, [
            'name'          => 'required',
            'duration'      => 'required',
            'description'   => 'required',
            'status'        => 'required|in:1,0',
            'free_shipping' => 'required|in:1,0',
            'shipping_type' => [Rule::requiredIf($request->input('free_shipping') == 0),Rule::in(['price_wise','weight_wise'])],
            'image'         => ['nullable','image',new FileExtentionCheckRule(file_format())]
        ]);


        $price_configuration = [];

        if($request->input('free_shipping') == 0){

            $key = $request->input('shipping_type') == "price_wise"
                                     ? 'price_base_zone_wise_price'
                                     : 'weight_base_zone_wise_price';
            $zoneWisePrices = $request->input($key);

            if(!$zoneWisePrices) return back()->with('error','Please create a zone first..');

            foreach ($zoneWisePrices as $zone_id => $costs) {
                foreach ($costs as $index => $cost) {

                    $lte =  @$request[$request->input('shipping_type')]["less_than_eq"][$index] ?? 0;
                    $gt  =  @$request[$request->input('shipping_type')]["greater_than"][$index] ?? 0;

                    if(!is_numeric(  $lte ) || !is_numeric(  $gt ) ) return back()->with('error',translate('Price must be numeric value'));
                    if( $lte  < 0 ||   $gt  < 0) return back()->with('error',translate('Price cannot be less than 0'));
                    if($gt > $lte) return back()->with('error',translate('Less than equal price must be higer than greater than prices'));

                    $price_configuration[] = [
                        "zone_id" => $zone_id,
                        "greater_than" => $gt,
                        "less_than_eq" => $lte,
                        "cost" => $cost
                    ];
                }
            }


        }

        $shipping = ShippingDelivery::findOrfail($id);

        $image = $shipping->image;
        if ($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['shipping_method']['path'] ,null,$image);
            } catch (\Exception $exp) {
            }
        }

        $shipping->name = $request->input("name");
        $shipping->duration = $request->input("duration");
        $shipping->description = $request->input("description");
        $shipping->free_shipping	 = $request->input("free_shipping");
        $shipping->shipping_type	 = $request->input("shipping_type");
        $shipping->status	 = $request->input("status");
        $shipping->image               = @$image;
        $shipping->price_configuration = $price_configuration;
        $shipping->save();


        return back()->with('success', translate('Shipping delivery has been updated'));
    }



    public function shippingCreate()
    {
        $title             = translate("Create Shipping delivery");
        $zones             = Zone::active()->get();
        return view('admin.shipping.create_delivery', compact('title','zones'));
    }





    /**
     * Shipping delivary edit
     *
     * @param int | string $id
     * @return View
     */
    public function shippingEdit(int | string $id): View
    {
        $title             = translate('Shipping delivery system update');
        $shippingDelivery  = ShippingDelivery::findOrFail($id);
        $zones         = Zone::active()->get();
        return view('admin.shipping.edit_delivery', compact('title', 'shippingDelivery', 'zones'));
    }




    /**
     * Delete a shipping delivary method
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function shippingDelete(Request $request): RedirectResponse
    {

        $shipping = ShippingDelivery::with(['order'])->where('id', $request->id)->first();


        if (count($shipping->order) == 0) {
            $shipping->delete();
            return back()->with('success', translate('Shipping Delivery Deleted'));
        }

        return back()->with('error', translate('This Shipping Delivary Has Order Under It ,Please Try again'));
    }



}
