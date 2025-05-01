<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlogController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:manage_blog']);
    }

    public function index() :View
    {
        $title = translate('Manage Blog');
        $blogs = Blog::search()->latest()->with('category')->paginate(site_settings('pagination_number',10));
        return view('admin.blog.index', compact('title', 'blogs'));
    }

    public function create() :View
    {
        $title      = translate('Blog create');
        $categories = Category::where('status', '1')->whereNull('parent_id')->get();
        return view('admin.blog.create', compact('title', 'categories'));
    }

    public function store(Request $request) :RedirectResponse
    {

        $data = $this->validate($request, [
            'category_id' => 'required|exists:categories,id',
            'image'       => ['required','image',new FileExtentionCheckRule(file_format())],
            'post'        => 'required',
            'body'        => 'required',
            'status'      => 'required|in:1,0',
        ]);
        if($request->hasFile('image')) {
            try {
                $data['image'] = store_file($request->image, file_path()['blog']['path']);
            }catch (\Exception $exp) {
            }
        }

        $data['body'] = build_dom_document( $data['body'],'blog_body'.rand(10,10000));
        Blog::create($data);

        return back()->with('success', translate("Blog has been created"));
    }

    public function edit(string $slug, int $id) :View
    {
        $title       = translate('Blog update');
        $categories  = Category::where('status', "1")->whereNull('parent_id')->get();
        $blog        = Blog::where('id',$id)->firstOrfail();
        return view('admin.blog.edit', compact('title', 'blog', 'categories'));
    }

    public function update(Request $request, int $id)
    {

        $data = $this->validate($request, [
            'category_id' => 'required|exists:categories,id',
            'post'        => 'required',
            'body'        => 'required',
            'image'       => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'status'      => 'required|in:1,0',
        ]);
        $blog  = Blog::where('id',$id)->firstOrfail();
        $image = $blog->image;
        if($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['blog']['path']);
            }catch (\Exception $exp) {

            }
        }
        $data['image'] = $image;
        $data['body']  = build_dom_document( $data['body'],'blog_body_update'.rand(10,10000));
        $blog->update($data);

        return back()->with('success', translate("Blog has been updated"));

    }

    public function show(int $id) :View
    {
        $title = translate('Blog Details');
        $blog  = Blog::with('category')->where('id', $id)->first();
        return view('admin.blog.details',compact('title','blog'));
    }

    public function delete(Request $request):RedirectResponse
    {
        $blog = Blog::where('id',$request->id)->firstOrfail();

        try {

            if($blog->image){
                remove_file(file_path()['blog']['path'],$blog->image);
            }
            $blog->delete();

        } catch (\Throwable $th) {

        }
        $blog->delete();
        return back()->with('success', translate("Blog has been deleted"));
    }

    public function statusUpdate(Request $request) :string {
        $request->validate([
            'data.id'=>'required|exists:blogs,id'
        ],[
            'data.id.required'=>translate('The Id Field Is Required')
        ]);
        $blog     = Blog::where('id',$request->data['id'])->first();
        $response = update_status($blog->id,'Blog',$request->data['status']);
        $response['reload'] = true;
        $response['reload'] = true;
        return json_encode([$response]);
    }


}
