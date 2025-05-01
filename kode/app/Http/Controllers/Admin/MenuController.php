<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Models\MenuCategory;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MenuController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:manage_frontend']);
    }


    public function index() :View
    {
        $title = translate('Manage Menu');
        $menus = Menu::get();
        return view('admin.menu.index', compact('title', 'menus'));
    }

    public function create() :View
    {
        $title = translate('Menu create');
        return view('admin.menu.create', compact('title'));
    }

    public function store(Request $request) :RedirectResponse
    {
        $this->validate($request, [
            'name'  => 'required|max:255',
            'image' => ['required','image',new FileExtentionCheckRule(file_format())],
            'url'   => 'nullable',
        ]);
        $image = null;
        if($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['menu']['path']);
            }catch (\Exception $exp) {

            }
        }
        Menu::create([
            'name'  => $request->name,
            'image' => $image,
            'slug'  => make_slug($request->name),
            'url'   => $request->url,
        ]);
        return back()->with('success',translate('Menu has been created'));
    }

    public function edit(int $id) :View
    {
        $title = translate('Menu Update');
        $menu  = Menu::where('id',$id)->firstOrfail();
        $categories = Category::whereNull('parent_id')->where('status', 1)->get();
        return view('admin.menu.edit', compact('title', 'menu', 'categories'));
    }

    public function update(Request $request, int $id) :RedirectResponse
    {

        $this->validate($request, [
            'name'  => 'required|max:255',
            'image' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'url'   => 'nullable',
        ]);
        $menu  = Menu::where('id',$id)->first();
        $image = $menu->image;

        if($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['menu']['path'], null, $image);
            }catch (\Exception $exp) {

            }
        }

        $menu->update([
            'name'  => $request->name,
            'image' => $image,
            'slug'  => make_slug($request->name),
            'url'  => $request->url,
        ]);
        return back()->with('success',translate('Menu has been updated'));
    }

    /**
     * menu delete method
     * @param $request
     */

    public function delete(Request $request) :RedirectResponse{

        $menu = Menu::where('id',$request->id)->firstOrfail();
        if($menu->default != 1){
            $menu->delete();
        }

        if($menu->image){
           try {
               remove_file(explode('x',  file_path()['menu']['path']),$menu->image);
           } catch (\Throwable $th) {

           }
        }

        return back()->with('success',translate('Menu Deleted'));
    }



    public function homeCategory(Request $request) :View{

        $categories     = Category::whereNull('parent_id')->where('status', '1')->get();
        $menucategories = MenuCategory::with('category')->get();
        $title = "Menu create";
        return view('admin.menu.home_category', compact('title','categories','menucategories'));

    }



    public function homeCategoryUpdate(Request $request) :RedirectResponse{


        MenuCategory::truncate();
        $categories = [];
        $data= [];

        if($request->category){
            foreach($request->category as $category){

                $data['category_id'] =  $category['cat_id'];
                $data['serial'] =  $category['serial'];
                $data['title'] =  $category['title'];
                array_push($categories,$data);
            }
            MenuCategory::insert($categories);
        }


        return back()->with('success',translate('Home category menu has been added'));
    }


}
