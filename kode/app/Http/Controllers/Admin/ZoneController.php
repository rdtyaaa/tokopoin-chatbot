<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Enums\StatusEnum;
use App\Models\CountryZone;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permissions:manage_zones']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $zones = Zone::with('countries')
                        ->when($search, fn ($query) => $query->where('name', 'like', "%$search%"))->paginate(paginate_number());

        $countries =  DB::table('countries')->get();

        $title = translate("Shipping Zone");

        return view('admin.shipping.zone.index', compact('title', 'zones', 'countries'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'name'        => 'required|string|max:255|unique:zones,name',
            'country_id.*' => 'required|numeric|exists:countries,id',
            'status'      =>  ['required', Rule::in(StatusEnum::toArray())]
        ], [
            'name.unique'       => translate('Already a Zone exists with the same name'),
            'country_id.exists' => translate('Invalid country Id')
        ]);

        $zone = Zone::create([
            'name'       => $request->input('name'),
            'status'     => $request->input('status')
        ]);

        $zone->countries()->attach($request->input('country_id'));

        return redirect()->route("admin.shipping.zone.index")->with('success', translate('Zone added successfully'));
    }

    public function status(Request $request)
    {

        $request->validate([
            'data.id'          => 'required|exists:zones,id'
        ], [
            'data.id.required' => translate('The Id Field Is Required')
        ]);

        $zone               = Zone::where('id', $request->data['id'])->first();
        $response           = update_status($zone->id, 'Zone', $request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }

    public function update(Request $request)
    {


        $request->validate([
            'name'          => ['required','string','max:255',Rule::unique('zones', 'name')->ignore($request->id)],
            'country_id.*'  => 'required|numeric|exists:countries,id',
        ], [
            'name.unique'       => translate('Already a Zone exists with the same name'),
            'country_id.exists' => translate('Invalid country Id')
        ]);

        $zone = Zone::findOrFail($request->id);

        $zone->name   = $request->input('name');
        $zone->save();

        $zone->countries()->sync($request->input('country_id'));

        return back()->with('success', translate('Zone updated successfully'));
    }

    public function destroy(string $id)
    {
        $zone = Zone::findOrFail($id);

        $zone->countries()->detach();
        $zone->delete();

        return back()->with('success', translate('State deleted successfully'));
    }
}
