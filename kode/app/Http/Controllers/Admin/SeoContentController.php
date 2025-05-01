<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeoContentController extends Controller
{
    public function __construct(){

        $this->middleware(['permissions:view_settings'])->only('index');
        $this->middleware(['permissions:update_settings'])->only('update');
    }

    /**
     * Seo setting view
     *
     * @return View
     */
    public function index() :View
    {
    	$title = translate('Seo Content');
    	$seo   = Frontend::where('slug', 'seo-section')->first();

    	return view('admin.setting.seo', compact('title', 'seo'));
    }


    /**
     * Update Seo settings
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request) :RedirectResponse
    {
    	$seo        = Frontend::where('slug', 'seo-section')->first();
        $seo_value  = json_decode($seo->value,true);


    	$seoImage   = $seo_value['seo_image']; $socialImage = $seo_value['social_image'];



        if($request->hasFile('seo_image')){
            try {
                $seoImage = store_file($request->seo_image, file_path()['seo_image']['path'], null, $seoImage);
            }catch (\Exception $exp){
                return back()->with('error',strip_tags($exp->getMessage()));
            }
        }
        if($request->hasFile('social_image')){
            try {
                $socialImage = store_file($request->social_image, file_path()['seo_image']['path'], null, $socialImage);

            }catch (\Exception $exp) {
                return back()->with('error',strip_tags($exp->getMessage()));
            }
        }

    	$data = [
    		'seo_image'          => $seoImage,
    		'meta_keywords'      => $request->meta_keywords,
    		'meta_description'   => build_dom_document($request->meta_description,"meta_description".rand(20,200)),
    		'social_title'       => $request->social_title,
    		'social_description' => $request->social_description,
    		'social_image'       => $socialImage
    	];

    	$seo->value = $data;
    	$seo->save();

        return back()->with('success',translate('Seo content has been update'));
    }
}
