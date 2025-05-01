<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Rules\Admin\TranslationUniqueCheckRule;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permissions:view_category'])->only('index');
        $this->middleware(['permissions:create_category'])->only('create','store');
        $this->middleware(['permissions:update_category'])->only('update','top');
        $this->middleware(['permissions:delete_category'])->only('delete');
    }

    public function index()
    {
        $title = translate('Manage Categories');
        $categories = Category::orderBy('serial', 'ASC')->with('parentCategory')->paginate(site_settings('pagination_number',10));
        return view('admin.category.index', compact('title', 'categories'));
    }

    public function top($id)
    {
        $category = Category::where('id', $id)->first();

        $category->top = $category->top == '1' ? '0' : '1';
        $category->save();

        return back()->with('success', translate("Category Top status has been updated"));
    }


    public function create()
    {
        $title = translate('Create category');
        $categories = Category::latest()->select('id', 'name','serial')->get();

        return view('admin.category.create', compact('title', 'categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'serial' => 'required|integer',
            'name' => ['required',new TranslationUniqueCheckRule('Category','name')],
            'parent_id' => 'nullable|exists:categories,id',
            'banner' => ['required','image',new FileExtentionCheckRule(file_format())],
            'image_icon' => ['required','image',new FileExtentionCheckRule(file_format())],
            'slug'    => 'required|max:191',
            'status' => 'required|in:1,0',
        ]);
        $banner = null;

        if($request->hasFile('banner')) {
            try {
                $banner = store_file($request->banner, file_path()['category']['path']);
            }catch (\Exception $exp) {

            }
        }
        if($request->hasFile('image_icon')) {
            try {
                $image_icon = store_file($request->image_icon, file_path()['category']['path']);
            }catch (\Exception $exp) {

            }
        }

        Category::create([
            'serial' => $request->serial,
            'name' => json_encode($request->name),
            'slug' => make_slug($request->slug),
            'parent_id' => $request->parent_id ?? null,
            'banner' => $banner,
            'image_icon' => @$image_icon,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_image' => $banner,
            'status' => $request->status,

        ]);

        return back()->with('success', translate("Category has been created"));
    }


    public function edit($id)
    {
        $title = translate('Categories Update');
        $categories = Category::orderBy('id', 'DESC')->select('id', 'name')->get();
        $category = Category::where('id', $id)->first();
        return view('admin.category.edit', compact('title', 'categories', 'category'));
    }

    public function update(Request $request, $id)
    {

        $size = explode('x',  file_path()['category']['size']);
        $this->validate($request, [
            'serial' => 'required|integer',
            'name' => ['required', new TranslationUniqueCheckRule('Category','name',$id)],
            'parent_id' => 'nullable|exists:categories,id',
            'banner' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'image_icon' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'slug'    => 'required|max:191',
            'status'  => 'required|in:1,0'
        ]);
        $category = Category::where('id', $id)->first();
        $banner = $category->banner;
        $metaImage = null;
        $image_icon = $category->image_icon;

        if($request->hasFile('banner')) {
            try {
                $banner = store_file($request->banner, file_path()['category']['path'], null, $banner);
            }catch (\Exception $exp) {

            }
        }

        if($request->hasFile('image_icon')) {
            try {
                $image_icon = store_file($request->image_icon, file_path()['category']['path'],null,$image_icon);
            }catch (\Exception $exp) {

            }
        }


        $category->update([
            'serial' => $request->serial,
            'name' =>json_encode($request->name),
            'slug' => make_slug($request->slug),
            'parent_id' => $request->parent_id ?? null,
            'banner' => $banner,
            'image_icon' => $image_icon,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_image' => $banner,
            'status' => $request->status,
        ]);


        return back()->with('success', translate("Category has been updated"));
    }

    public function delete($id)
    {
        $category = Category::where('id', $id)->first();
        if(count($category->product) > 0){
            return back()->with('error', translate("Please Delete All relational Data, then try again"));
        } else {
            $category->delete();
            return back()->with('success', translate("Category has been Deleted"));
        }
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'search' => 'required',
        ]);
        $search = $request->search;
        $title = "Search by -" . $search;
        $categories = Category::where('name', 'like', "%$search%")->paginate(site_settings('pagination_number',10));
        return view('admin.category.index', compact('search', 'categories', 'title'));
    }
}
