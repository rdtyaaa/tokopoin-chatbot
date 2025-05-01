<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permissions:manage_taxes']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $taxes = Tax::when($search, fn ($query) => $query->where('name', 'like', "%$search%"))->orderBy('created_at', 'desc')->get();

        $title = translate("Manage Taxes");

        return view('admin.tax.index', compact('title', 'taxes'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'name'        => 'required|string|max:255|unique:taxes,name',
            'status'      =>  ['required', Rule::in(StatusEnum::toArray())]
        ]);

        Tax::create([
            'name'       => $request->input('name'),
            'status'     => $request->input('status')?? StatusEnum::true->status()
        ]);


        return back()->with('success', translate('Tax added successfully'));
    }

    public function statusUpdate(Request $request)
    {

        $request->validate([
            'data.id'          => 'required|exists:taxes,id'
        ], [
            'data.id.required' => translate('The Id Field Is Required')
        ]);

        $tax                = Tax::where('id', $request->data['id'])->first();
        $response           = update_status($tax->id, 'Tax', $request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }

    public function update(Request $request)
    {

        $request->validate([
            'id'          => 'required|exists:taxes,id',
            'name'        => 'required|string|max:255|unique:taxes,name,'.$request->input('id'),
            'status'      =>  ['required', Rule::in(StatusEnum::toArray())]
        ], [
            'name.unique'       => translate('Already a Zone taxes with the same name'),

        ]);


        Tax::where('id',$request->input('id'))->update([
            'name'       => $request->input('name'),
            'status'     => $request->input('status')?? StatusEnum::true->status()
        ]);


        return back()->with('success', translate('Tax updated successfully'));
    }

    public function delete(string $id)
    {
        $tax = Tax::withCount(['products'])->findOrFail($id);

        if($tax->products_count > 0) return back()->with('error', translate('Tax has products.')); 
        $tax->delete();

        return back()->with('success', translate('Tax deleted successfully'));
    }
}
