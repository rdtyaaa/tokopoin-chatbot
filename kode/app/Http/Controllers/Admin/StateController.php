<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;


class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permissions:manage_states']);
    }


    public function index(Request $request,$id = null)
    {
        $search = $request->input('search');

        $states = State::latest()->with('country')->when($search,fn ($query) => $query->where('name', 'like', "%$search%")
                       ->orWhereHas('country', fn ($q) => $q->where('name', 'like', "%$search%")))->paginate(paginate_number());

        $countries  =  DB::table('countries')->get();

        $title = translate("Shipping State");

        return view('admin.shipping.state.index', compact('title', 'states', 'countries'));
    }





    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'country_id'  => 'required|numeric|exists:countries,id',
            'status'      =>  ['required', Rule::in(StatusEnum::toArray())]
        ]);

        State::create([
            'name'       => $request->input('name'),
            'country_id' => $request->input('country_id'),
            'status'     => $request->input('status')
        ]);

        return redirect()->route("admin.shipping.state.index")->with('success', translate('State added successfully'));
    }



    public function status(Request $request)
    {

        $request->validate([
            'data.id'          => 'required|exists:states,id'
        ], [
            'data.id.required' => translate('The Id Field Is Required')
        ]);

        $state              = State::where('id', $request->data['id'])->first();
        $response           = update_status($state->id, 'State', $request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'          => 'required',
            'name'        => 'required|string|max:255',
            'country_id'  => 'required|numeric|exists:countries,id',

        ]);

        $state = State::findOrFail($request->id);

        $state->name = $request->input('name');
        $state->country_id = $request->input('country_id');
        $state->save();

        return  back()->with('success', translate('State updated successfully'));
    }

    public function destroy(int $id)
    {
        $state = State::withCount(['cities','userAddresses'])->findOrFail($id);


        if ($state->cities_count > 0 || $state->user_addresses_count > 0) {
            return redirect()->back()->with('error', translate('You cannot delete this state because it has associated cities or user address'));
        }
        $state->delete();

        return back()->with('success', translate('State deleted successfully'));
    }
}
