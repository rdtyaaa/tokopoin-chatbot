<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Country;


class CountryController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:manage_countries']);
    }


    public function index(Request $request)
    {
        $title = translate("Shipping Countries");

        $countries = DB::table('countries')
                        ->when($request->search, fn($query) => $query->where('name', 'like', '%' . $request->search . '%'))
                        ->paginate(paginate_number());

        return view('admin.shipping.countries', compact('title', 'countries'));
    }



    public function status(Request $request)
    {
        $request->validate([
            'data.id'          => 'required|exists:countries,id'
        ], [
            'data.id.required' => translate('The Id Field Is Required')
        ]);

        $country            = Country::where('id', $request->data['id'])->first();
        $response           = update_status($country->id, 'Country', $request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }
}
