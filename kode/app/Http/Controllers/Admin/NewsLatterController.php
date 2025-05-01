<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsLatter;
use Illuminate\Http\Request;
use Image;
class NewsLatterController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:view_settings'])->only('index','cacheClear','systemInfo');
        $this->middleware(['permissions:update_support'])->only('update');
    }
    public function index(){
        $title = translate('News Letter');
        $newsLatter =  NewsLatter::latest()->first();
        return view('admin.news_latter.index',compact('newsLatter','title'));
    }
    public function update(Request $request){
        $request->validate([
            'heading'=>'required',
            'time_duration'=>'required',
            'discount_percentage'=>'required',
            'banner_image' => 'nullable|image|mimes:jpg,png,jpeg',
            'time_unit'=>'required',
        ]);
        $newsLatter =  NewsLatter::latest()->first();
        $newsLatter->heading =  $request->heading;
        $newsLatter->time_duration = $request->time_duration;
        $newsLatter->discount_percentage =  $request->discount_percentage;
        $newsLatter->time_unit =  $request->time_unit;
        $newsLatter->status =  $request->status;
        $newsLatter->discount =  $request->discount;
        $newsLatter->description =  $request->description;
        if($request->hasFile('banner_image')) {
            try{
                $path = file_path()['newsLatter']['path'];
                $image = Image::make(file_get_contents($request->banner_image));
                $image->save($path.'/'.'default.jpg');
            }catch (\Exception $exp) {

            }
        }
        $newsLatter->save();
        return back()->with('success',translate('NewsLatter has been updated'));
    }
}
