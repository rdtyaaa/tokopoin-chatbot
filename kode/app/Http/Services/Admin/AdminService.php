<?php

namespace App\Http\Services\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class AdminService extends Controller
{



    /**
     * Get list of staff
     *
     * @return Collection
     */
    public function index() : Collection {
        return Admin::where('id','!=',1)->with(['role','createdBy'])->latest()->get();
    }


    /**
     * Create an admin staff
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request) :array {

        $image = null;
        if($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['profile']['admin']['path']);
            }catch (\Exception $exp) {
            }
        }
        $admin = Admin::create([
            'role_id'      => $request->role,
            'name'         => $request->name,
            'image'        => @$image ,
            'user_name'    => $request->user_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'created_by'   => auth_user()->id,
            'address'      => $request->address,
            'password'     => Hash::make($request->password),
            'status'       => (StatusEnum::true)->status()
        ]);
        $response['status']  = "success";
        $response['message'] = translate('Staff Created Succesfully');
        $response['data']    = $admin;

        return  $response;
    }



    /**
     * Update a admin
     *
     * @param Request $request
     * @return array
     */
    public function update(Request $request) :array  {

        $admin = $this->admin($request->id);
        $image = $admin->image;
        if($request->hasFile('image')) {
            try {
                $image = store_file($request->image, file_path()['profile']['admin']['path'],null,$admin->image);
            }catch (\Exception $exp) {
            }
        }
        $admin = $admin->update([
            'role_id'    => $request->role,
            'name'       => $request->name,
            'image'      => $image ,
            'user_name'  => $request->user_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'status'     => (StatusEnum::true)->status()
        ]);
        $response['status']  = "success";
        $response['message'] = translate('Staff Updated Succesfully');
        $response['data']    = $admin;

        return $response;
    }


    /**
     * Get admin by id
     * 
     * @param int | string $id 
     * 
     * @return Admin 
     */
    public function admin(int | string $id) : Admin{
       return Admin::where('id',$id)->first();   
    }


    


    /**
     * Delete admin by id
     * 
     * @param int | string $id 
     * 
     * @return array 
     */
    public function destory(int | string $id) :array {

        $response['status']  = 'success';
        $response['message'] = translate('Deleted Successfully');
        try {
            $admin = $this->admin($id);
            if($admin->image){
                remove_file(file_path()['profile']['admin']['path'],$admin->image);
            }
            $admin->delete();

        } catch (\Throwable $th) {
            $response['status']  = 'error';
            $response['message'] = translate('Post Data Error !! Can Not Be Deleted');
        }
        return $response;
    }

   
}