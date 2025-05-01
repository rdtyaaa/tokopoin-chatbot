<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Models\Frontend;
use App\Models\Testimonial;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function frontend() :View
    {
        $title     = translate('Manage frontend section');
        $frontends = Frontend::whereNotIn('slug',['seo-section','promotional-offer','promotional-offer-2'])->get();

        return view('admin.frontend.index', compact('title', 'frontends'));
    }


    public function promotionalBanner()
    {
        $title     = translate('Manage Banner');
        $frontends = Frontend::whereIn('slug',['promotional-offer','promotional-offer-2'])->get();

        return view('admin.frontend.index', compact('title', 'frontends'));
    }

    public function frontendUpdate(Request $request, int $id) :RedirectResponse | array
    {
        $frontends       = Frontend::where('id',$id)->firstOrFail();
        $section_values  = json_decode($frontends->value,true);
        $input_data      = $request->frontend;
        $validation      = [];

        if(isset( $section_values['image']) ){

            $validation = [
                'frontend.image.value'   => ['nullable','image',new FileExtentionCheckRule(file_format())],
            ];
           $input_data['image']['value'] = $section_values['image']['value'];
        }
        if(isset($section_values['image_2']) ){

            $validation = [
                'frontend.image.value'     => ['nullable','image',new FileExtentionCheckRule(file_format())],
            ];
            $input_data['image_2']['value'] = $section_values['image_2']['value'];
        }


        if($request->hasFile('frontend.image.value')){
            $request->validate($validation);
            try{
                $image = store_file($request->file('frontend.image.value'), file_path()['frontend']['path'],null, $section_values['image']['value']);

            }catch (\Exception $exp){

            }

            $input_data['image']['value'] =    $image ;

        }
        if($request->hasFile('frontend.image_2.value')){
            $request->validate($validation);
            try{
                $image = store_file($request->file('frontend.image_2.value'), file_path()['frontend']['path'],null, $section_values['image_2']['value']);

            }catch (\Exception $exp){
                $image =  '@@';
            }

            $input_data['image_2']['value'] =    $image ;

        }



        $frontends->status =  $request->status ? $request->status :"0";
        $frontends->value  = json_encode($input_data);
        $frontends->save();

        return request()->ajax() ? [
            'status'  => true,
            'message' => translate("Frontend Section Updated Successfully"),
        ] : back()->with('success',translate("Frontend Section Updated Successfully"));

    }


     public function banner() :View
    {
       $title    = translate('Banner Item');
       $banners  =  Banner::orderBy('id','asc')->get();
       return view('admin.banner.index',compact('title','banners'));
    }

     public function bannerCreate() :View
    {
       $title = translate('Banner create');
       return view('admin.banner.create',compact('title'));
    }

     public function bannerStore(Request $request) :RedirectResponse
    {


        $request->validate([
            'serial_id'    =>'required',
            'btn_url'      =>'required',
            'banner_image' => ['image',new FileExtentionCheckRule(file_format())],
            'status'       => 'required|in:1,0',

        ]);

        $banner = new Banner();
        $banner->serial_id     = $request->serial_id;
        $banner->btn_url       = $request->btn_url;
        $banner->status        = $request->status;
        $image = '@@';
        if($request->hasFile('banner_image')){
            try{
                $image = store_file($request->banner_image, file_path()['banner_image']['path']);
            }catch (\Exception $exp){

            }
        }
        $banner->bg_image = $image;
        $banner->save();

        return back()->with('success',translate("banner created successfully"));

    }



     public function bannerEdit(int $id) :View {

        $title = translate('Banner edit');
        $banner = Banner::where('id',$id)->firstOrfail();
        return view('admin.banner.edit',compact('title','banner'));
     }




     public function bannerUpdate(Request $request) :RedirectResponse {

        $request->validate([
            'serial_id'    =>'required',
            'banner_image' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'status'       => 'required|in:1,0',

        ]);
        $banner = Banner::where('id', $request->id)->firstOrfail();
        $banner->serial_id      = $request->input('serial_id');
        $banner->btn_url        = $request->input('btn_url');
        $banner->status         = $request->input('status');
        $image = $banner->bg_image;
        if($request->hasFile('banner_image')){
            try{
                $image = store_file($request->banner_image, file_path()['banner_image']['path'],null, $banner->bg_image);
            }catch (\Exception $exp){

            }
        }
        $banner->bg_image=$image;
        $banner->save();

        return back()->with('success',translate("banner Updated successfully"));
    }


     public function bannerDelete(Request $request) :RedirectResponse{

            $banner = Banner::where('id',$request->id)->firstOrfail();
            if($banner->bg_image)
            {
                try {
                    remove_file(file_path()['banner_image']['path'],$banner->bg_image);
                } catch (\Throwable $th) {

                }
            }
            $banner->delete();

        return back()->with('success',translate("banner Deleted successfully"));
    }



    /** testimonial section */


    public function testimonial() :View
    {
        $title         = translate('Manage Testimonials');
        $testimonials  = Testimonial::when(request()->input("search"),function($q){
               $searchTerm = "%".request()->input("search")."%";
               return $q->where('author','like',$searchTerm)
                         ->orwhere('designation','like',$searchTerm);
        })->latest()->get();

        return view('admin.frontend.testimonial', compact('title', 'testimonials'));
    }



    public function testimonialStore(Request $request) :RedirectResponse
    {
        $request->validate([
            'author'       => 'required|max:191',
            'designation'  => 'required|max:191',
            'quote'        => 'required',
            'rating'       => 'required|gt:0|lte:5',
            'image'        => ['nullable','image',new FileExtentionCheckRule(file_format())],

        ]);


        $testimonial               =  new Testimonial();
        $testimonial->author       =  $request->author;
        $testimonial->quote        =  $request->quote;
        $testimonial->rating       =  $request->rating;
        $testimonial->designation  =  $request->designation;
        $testimonial->status       =  1;


        if($request->hasFile('image')){
            try{
                $image = store_file($request->image, file_path()['testimonial']['path']);
            }catch (\Exception $exp){

            }
        }

        $testimonial->image = @$image;
        $testimonial->save();


       return back()->with("success",translate('Testimonial Added Successfully'));
    }



    public function testimonialUpdate(Request $request) :RedirectResponse
    {
        $request->validate([
            'id'           => 'required|exists:testimonials,id',
            'designation'  => 'required|max:191',
            'quote'        => 'required',
            'rating'       => 'required|gt:0|lte:5',
            'image'        => ['nullable','image',new FileExtentionCheckRule(file_format())],

        ]);


        $testimonial               =  Testimonial::findOrfail($request->id);
        $testimonial->author       =  $request->author;
        $testimonial->quote        =  $request->quote;
        $testimonial->rating       =  $request->rating;
        $testimonial->designation  =  $request->designation;
        $testimonial->status       =  1;
        $image                     =  $testimonial->image;

        if($request->hasFile('image')){
            try{
                $image = store_file($request->image, file_path()['testimonial']['path'],null,$image );
            }catch (\Exception $exp){

            }
        }

        $testimonial->image = @$image;
        $testimonial->save();


       return back()->with("success",translate('Testimonial Updated Successfully'));
    }


    public function testimonialDelete(Request $request) :RedirectResponse{

            $testimonial =  Testimonial::findOrfail($request->id);
            if($testimonial->image){
                try {
                    remove_file(file_path()['testimonial']['path'],$testimonial->image);
                } catch (\Throwable $th) {

                }
            }
            $testimonial->delete();

        return back()->with('success',translate("Testimonial Deleted successfully"));
    }


    /**
     * Update testimonial status
     *
     * @param Request $request
     * @return string
     */
    public function testimonialStatusUpdate(Request $request) :string {

        $request->validate([
            'data.id'=>'required|exists:testimonials,id'
        ],[
            'data.id.required'=>translate('The Id Field Is Required')
        ]);

        $testimonial        = Testimonial::findOrfail($request->data['id']);
        $response           = update_status($testimonial->id,'Testimonial',$request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }




}
