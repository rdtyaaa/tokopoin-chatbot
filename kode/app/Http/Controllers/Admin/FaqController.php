<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{

    public function __construct(){

        $this->middleware(['permissions:view_support'])->only('index');
        $this->middleware(['permissions:create_support'])->only('crerate','store');
        $this->middleware(['permissions:update_support'])->only('update');
        $this->middleware(['permissions:delete_support'])->only('delete');
    }
    public function index()
    {
        $title  = translate('FAQ');
        $faqs   = Faq::when(request()->input('search'),function($q){
                $searchBy = '%'. request()->input('search').'%';
                return $q->where('support_category','like',$searchBy)
                          ->orWhere('question','like',$searchBy)
                          ->orWhere('answer','like',$searchBy);

        })->orderBy('id','DESC')->get();
        return view('admin.faq.index', compact('title','faqs'));
    }

    public function crerate() :View
    {
        $title = translate('Create Faq');
        return view('admin.faq.create', compact('title'));
    }

    public function store(Request $request) :RedirectResponse
    {
        $this->validate($request, [
            'support_category' => 'required',
            'question'         => 'required',
            'answer'           => 'required',
            'status'           => 'required'
        ]);
        $faq                    =  new Faq();
        $faq->support_category  =  $request->support_category;
        $faq->question          =  $request->question;
        $faq->answer            =  $request->answer;
        $faq->status            =  $request->status;
        $faq->save();
        return back()->with('success',translate('Faq has been Created Successfully'));
    }

    public function update(Request $request) : RedirectResponse
    {
        $this->validate($request, [
            'support_category' => 'required',
            'question'         => 'required',
            'answer'           => 'required',
            'status'           => 'required',
        ]);

        $faq                   =  Faq::where('id',$request->id)->first();
        $faq->support_category =  $request->support_category;
        $faq->question         =  $request->question;
        $faq->answer           =  $request->answer;
        $faq->status           =  $request->status;
        $faq->save();

        return back()->with('success',translate('Faq has been Updated Successfully'));
    }


    public function delete(Request $request) :RedirectResponse
    {
        Faq::where('id',$request->id)->delete();
        return back()->with('success',translate('Faq has been Deleted Successfully'));
    }





}
