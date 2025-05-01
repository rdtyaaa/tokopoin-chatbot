<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Enums\StatusEnum;
use Illuminate\Http\RedirectResponse;

class CityController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:manage_cities']);
    }


    public function index(Request $request)
    {
        $search = $request->input('search');

        $cities = City::latest()->with('state.country')->when($search,fn ($query) => $query->where('name', 'like', "%$search%")
                    ->orWhereHas('state',fn ($q) => $q->where('name', 'like', "%$search%")
                    ->orWhereHas('country', fn ($q2) => $q2->where('name', 'like', "%$search%"))))->paginate(paginate_number());


        $states    =  DB::table('states')->latest()->get();

        $title     = translate("Shipping City");

        return view('admin.shipping.city.index', compact('title', 'cities', 'states'));
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'state_id'    => 'required|numeric|exists:states,id',
            'status'      =>  ['required', Rule::in(StatusEnum::toArray())],
            'shipping_fee'=> 'required|numeric'
        ]);

        City::create([
            'name'        => $request->input('name'),
            'state_id'    => $request->input('state_id'),
            'status'      => $request->input('status'),
            'shipping_fee'=> $request->input('shipping_fee')
        ]);

        return redirect()->route("admin.shipping.city.index")->with('success', translate('City added successfully'));
    }


    public function status(Request $request)
    {

        $request->validate([
            'data.id'          => 'required|exists:cities,id'
        ], [
            'data.id.required' => translate('The Id Field Is Required')
        ]);

        $state              = City::where('id', $request->data['id'])->first();
        $response           = update_status($state->id, 'City', $request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }

    public function update(Request $request)
    {

        $request->validate([
            'name'        => 'required|string|max:255',
            'state_id'    => 'required|numeric|exists:states,id',
            'shipping_fee'=> 'required|numeric'
        ]);

        $city = city::findOrFail($request->id);

        $city->name        = $request->input('name');
        $city->state_id    = $request->input('state_id');
        $city->shipping_fee= $request->input('shipping_fee');
        $city->save();

        return back()->with('success', translate('City updated successfully'));
    }

    public function destroy(string $id)
    {
        $city = City::withCount(['userAddresses'])->findOrFail($id);

        if ($city->user_addresses_count > 0) {
            return redirect()->back()->with('error', translate('You cannot delete this state because it has associated  user address'));
        }
        $city->delete();
        return back()->with('success', translate('City deleted successfully'));
    }
}
