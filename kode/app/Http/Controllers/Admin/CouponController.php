<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:manage_cuppon']);
    }


    public function index() :View
    {
        $title   = translate('Manage Coupons');
        $coupons = Coupon::latest()->paginate(site_settings('pagination_number',10));
        return view('admin.promote.coupon.index', compact('title', 'coupons'));
    }

    public function create() :View
    {
        $title = translate('Coupon create');
        return view('admin.promote.coupon.create', compact('title'));
    }

    public function store(Request $request) :RedirectResponse
    {
        $this->validate($request, [
            'name'        => 'required',
            'code'        => 'required|unique:coupons,code',
            'type'        => 'required|in:1,2',
            'value'       => 'required|numeric|gt:0',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'status'      => 'required|in:1,2'
        ]);
        $coupon              = new Coupon();
        $coupon->name        = $request->name;
        $coupon->code        = $request->code;
        $coupon->type        = $request->type;
        $coupon->value       = $request->value;
        $coupon->start_date  = $request->start_date;
        $coupon->end_date    = $request->end_date;
        $coupon->status      = $request->status;
        $coupon->save();
        return back()->with('success',translate('Coupon has been created'));
    }

    public function edit(int $id) :View
    {
        $title   = translate('Coupon update');
        $coupon  = Coupon::where('id',$id)->first();
        return view('admin.promote.coupon.edit', compact('title', 'coupon'));
    }

    public function update(Request $request, int  $id) :RedirectResponse
    {

        $this->validate($request, [
            'name'       => 'required',
            'code'        => 'required|unique:coupons,code,'.$id,

            'type'       => 'required|in:1,2',
            'value'      => 'required|numeric|gt:0',

            'start_date' => 'date',
            'end_date'   => 'date|after:start_date',
            'status'     => 'required|in:1,2'
        ]);

        $coupon             = Coupon::where('id', $id)->firstOrfail();
        $coupon->name       = $request->name;
        $coupon->code       = $request->code;
        $coupon->type       = $request->type;
        $coupon->value      = $request->value;
        $coupon->start_date = $request->start_date;
        $coupon->end_date   = $request->end_date;
        $coupon->status     = $request->status;
        $coupon->save();
        return back()->with('success',translate('Coupon has been updated'));
    }

    public function couponDelete(Request $request) :RedirectResponse
    {
        Coupon::where('id', $request->id)->delete();
        return back()->with('success',translate('Coupon has been deleted'));
    }
}
