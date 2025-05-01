<?php

namespace App\Http\Services\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandService extends Controller
{

    public function index()
    {
        return Brand::with(['createdBy'])->latest()->paginate(site_settings('pagination_number',10));
    }

    public function store($request)
    {

        $logo = null;
        if($request->hasFile('logo')) {
            try {
                $logo = store_file($request->image, file_path()['brand']['path']);
            }catch (\Exception $exp) {
            }
        }
        $brand = Brand::create([
            'name' => $request->name,
            'slug' => make_slug($request->name),
            'logo' => $logo,
            'created_by' => auth_user()->id,
            'status'=> (StatusEnum::true)->status(),
            'top'=> (StatusEnum::false)->status()
        ]);
        $response['status'] = "success";
        $response['message'] = translate('Brand Created Succesfully');
        $response['data'] = $brand;
        return  $response;
    }


    public function update($request)
    {
        $role = $this->role($request->id);
        $role = $role->update([
            'name' => $request->name,
            'slug' => make_slug( $request->name),
            'permissions' => json_encode($this->formatPermission($request)),
        ]);
        $response['status'] = "success";
        $response['message'] = translate('Roles Updated Succesfully');
        $response['data'] = $role;
        return $response;
    }

    public function brand($id){
      return Brand::withCount(['product'])->where('id',$id)->first();   
    }

    public function destory($id)
    {

        $response['status'] = 'error';
        $response['message'] = translate('This Brand Has Product !!!');
        try {
            $brand = $this->role($id);
            if($brand->product_count == 0){
                if($brand->logo){
                    remove_file(file_path()['brand']['path'],$brand->logo);
                }
                $response['status'] = 'success';
                $response['message'] = translate('Deleted Successfully');
                $brand->delete();
            }
        
        } catch (\Throwable $th) {
      
        }
        return $response;
    }


  
}