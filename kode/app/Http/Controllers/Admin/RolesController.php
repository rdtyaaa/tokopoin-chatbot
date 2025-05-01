<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolesRequest;
use App\Http\Services\Admin\RoleService;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolesController extends Controller
{
    public $roleService;
    public function __construct()
    {
        $this->roleService = new RoleService();
        $this->middleware(['permissions:view_roles'])->only('index');
        $this->middleware(['permissions:create_roles'])->only('create','store');
        $this->middleware(['permissions:update_roles'])->only('update','edit','statusUpdate');
        $this->middleware(['permissions:delete_roles'])->only('destroy');
    }



    /**
     * View all roles
     *
     * @return View
     */
    public function index() :View
    {
        return view('admin.roles.index', [
            'title' =>  translate("Manage Roles"),
            'roles' =>   $this->roleService->index(),
        ]);
    }


    /**
     * Create  a new role
     *
     * @return View
     */
    public function create():View
    {
        return view('admin.roles.create', [

            'title'       =>  translate("Role Create"),
            'permissions' =>   $this->roleService->permissions(),
        ]);
    }


    /**
     * Store a new role
     *
     * @param RolesRequest $request
     * @return RedirectResponse
     */
    public function store(RolesRequest $request) :RedirectResponse
    {
        $response = $this->roleService->store($request);
        return back()->with($response['status'],$response['message']);
    }





    /**
     * Edit a specific role
     *
     * @param int | string $id
     * @return View
     */
    public function edit(int | string $id) :View {

        return view('admin.roles.edit', [
            'title'       =>  translate("Update Role"),
            'permissions' =>   $this->roleService->permissions(),
            'role'        =>   $this->roleService->role($id),
        ]);
    }


    /**
     * Update a specific role
     *
     * @param RolesRequest $request
     * @return RedirectResponse
     */
    public function update(RolesRequest $request):RedirectResponse {

        $response = $this->roleService->update($request);
        return back()->with($response['status'],$response['message']);
    }


    /**
     * Update a specific role status
     *
     * @param Request $request
     * @return string
     */
    public function statusUpdate(Request $request) :string {

    

        $request->validate([
            'data.id'          => 'required|exists:roles,id'
        ],[
            'data.id.required' => translate('The Id Field Is Required')
        ]);
        $role               = Role::where('id',$request->data['id'])->first();
        $response           = update_status($role->id,'Role',$request->data['status']);
        $response['reload'] = true;
        return json_encode([$response]);
    }





    /**
     * Destory a specific role
     *
     * @param int | string  $id
     * @return RedirectResponse
     */
    public function destroy($id):RedirectResponse {
        $response = $this->roleService->destory($id);
        return back()->with( $response['status'],$response['message']);
    }
}
