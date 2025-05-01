<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageSetup;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageSetUpController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:view_settings'])->only('index');
        $this->middleware(['permissions:create_settings'])->only('create',"store");
        $this->middleware(['permissions:update_settings'])->only('edit','update','delete');
    }

    /**
     * View all pages
     *
     * @return View
     */
    public function index() :View
    {
        $title = translate("Manage Page setup");

        $pageSetups = PageSetup::latest()->paginate(site_settings('pagination_number',10));
        return view('admin.page_setup.index', compact('title', 'pageSetups'));
    }


    /**
     * Create a new page
     *
     * @return View
     */
    public function create() :View
    {
        $title = translate("Add new page");
        return view('admin.page_setup.create', compact('title'));
    }


    /**
     * Store a new page
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request) :RedirectResponse
    {
        $data = $this->validate($request, [
            'name'         => 'required|max:255',
            'description'  => 'required'
        ]);

        $data ['description'] = build_dom_document($data ['description'],'page_create'.rand(20,30000));

        PageSetup::create($data);
        return back()->with('success',translate('Frontend new page has been created.'));
    }


    /**
     * Edit a specofc page
     *
     * @param string $slug
     * @param  int | string $id
     * @return View
     */
    public function edit(string $slug, int | string $id) :View
    {
        $title = translate("Frontend page update");
        $data  = PageSetup::where('id', $id)->firstOrFail();
        return view('admin.page_setup.edit', compact('title', 'data'));
    }


    
    /**
     * Update a page
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id) :RedirectResponse
    {
        $data = $this->validate($request, [
            'name'        => 'required',
            'description' => 'required'
        ]);
        $page = PageSetup::findorFail($id);
        $data ['description'] = build_dom_document($data ['description'],'page_update'.rand(20,30000));
        $page->update($data);
        return back()->with('success',translate('Frontend page has been updated.'));
    }


    /**
     * Update a specific page status
     *
     * @param Request $request
     * @return string
     */
    public function statusUpdate(Request $request) :string {

        $request->validate([
            'data.id'          => 'required|exists:page_setups,id'
        ],[
            'data.id.required' => translate('The Id Field Is Required')
        ]);
        $page               = PageSetup::where('id',$request->data['id'])->first();
        $response           = update_status($page->id,'PageSetup',$request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }


    /**
     * Delete a specific page
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request) :RedirectResponse
    {
        $page = PageSetup::findorFail($request->id);
        $page->delete();
        return back()->with('success',translate('Page has been deleted.'));
    }
   

}
