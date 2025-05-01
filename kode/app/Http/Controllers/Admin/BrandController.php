<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Language;
use App\Rules\Admin\TranslationUniqueCheckRule;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\Request;

class BrandController extends Controller
{
  public $brandService;
    public function __construct()
    {
        $this->middleware(['permissions:view_brand'])->only('index','search');
        $this->middleware(['permissions:create_brand'])->only('create','store','search');
        $this->middleware(['permissions:update_brand'])->only('update');
        $this->middleware(['permissions:delete_brand'])->only('brandDelete');
    }

    public function index()
    {
        $title = translate('All Brands');
        $brands = Brand::orderBy('serial', 'ASC')->paginate(site_settings('pagination_number',10));
        return view('admin.brand.index', compact('title', 'brands'));
    }
    public function create()
    {
        $title = translate('Create Brands');
        $languages = Language::active()->get();
        return view('admin.brand.create', compact('title',"languages"));
    }
    public function store(Request $request)
    {
        $this->validate($request,[
                'serial'  => 'required|integer',
                'title.'.session()->get("locale") => ['required' , new TranslationUniqueCheckRule('Brand','name')],
                'logo'    => ['required','image',new FileExtentionCheckRule(file_format())],
                'slug'    => 'required|max:191',
                'status'  => 'required|in:1,2',
            ],
            [
                'title.'.session()->get("locale") => session()->get("locale") .translate(' Title Is Required')
            ]
        );
        $logo = null;
        if($request->hasFile('logo')) {
            try {
                $logo = store_file($request->logo, file_path()['brand']['path']);
            }catch (\Exception $exp) {
                return back()->with('error', translate("Image could not be uploaded."));
            }
        }
        Brand::create([
            'serial' => $request->serial,
            'name' => json_encode($request->title),
            'logo' => $logo,
            'slug' => make_slug($request->slug),
            "top"=>0,
            'status' => $request->status,
        ]);

        return back()->with('success', translate("Brand has been created"));
    }

    public function update(Request $request)
    {

        $this->validate($request, [
            'serial' => 'required|integer',
            'title.'.session()->get("locale") => ['required' , new TranslationUniqueCheckRule('Brand','name',$request->id)],
            'logo' =>[ new FileExtentionCheckRule(file_format())],
            'status' => 'required|in:1,2',
            'slug'    => 'required|max:191',
        ]);
        $brand = Brand::where('id',$request->id)->firstOrfail();
        $title =  json_decode($brand->name,true);

        $title[get_system_locale()] = $request->title[get_system_locale()];

        $logo = $brand->logo;
        if($request->hasFile('logo')) {
            try {
                $logo = store_file($request->logo, file_path()['brand']['path'], file_path()['brand']['size'], $logo);
            }catch (\Exception $exp){
                return back()->with('error',translate("Image could not be uploaded."));
            }
        }
        $brand->update([
            'serial' => $request->serial,
            'name' => json_encode( $title ),
            'logo' => $logo,
            'slug' => make_slug($request->slug),
            'status' => $request->status,
        ]);

        return back()->with('success', translate("Brand has been updated"));
    }

    public function brandDelete(Request $request)
    {
        $brand = Brand::where('id', $request->id)->first();
        if(count($brand->product) > 0){
            return back()->with('error', translate("Please Delete All relational Data, then try again"));
        }

        $brand->delete();

        return back()->with('success', translate("Brand deleted succesfully"));

    }

    public function top($id)
    {
        $brand = Brand::where('id', $id)->first();
        $brand->top = $brand->top == 1 ? Brand::YES : Brand::NO;
        $brand->save();

        return back()->with('success', translate("Brand top status has been updated"));
    }


    public function search(Request $request)
    {
        $search = $request->search;
        $title = "Search by -" . $search;
        $brands = Brand::where('name', 'like', "%$search%")->paginate(site_settings('pagination_number',10));
        return view('admin.brand.index', compact('search', 'brands', 'title'));
    }
}
