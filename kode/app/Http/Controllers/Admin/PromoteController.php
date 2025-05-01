<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Rules\General\FileExtentionCheckRule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PromoteController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:manage_offer'])
                        ->only('flashDeals','flashDealCreate','flashDealDelete','flashDealUpdate','flashDealEdit');

    }

 


    /**
     * Flash deals
     *
     * @return void
     */
    public function flashDeals() :view | JsonResponse {
        

        if(request()->ajax()){

            $page      = request()->input('page',0);

            $products  =   Product::with('category', 'brand', 'subCategory', 'order')
                                ->select('name','id')
                                ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                ->inhouseProduct()
                                ->physical()
                                ->orderBy('id', 'DESC')
                                ->paginate(site_settings('pagination_number',10), ['*'], 'page', $page);
                                
            $currentPage     = $products->perpage() * $products->currentpage() > $products->total()  
                                    ? $products->total()  
                                    :  $products->perpage() * $products->currentpage();
                    

            return response()->json([
                'data'                => $products,
                'total_data'          => $products->total(),
                'current_page_data'   => $currentPage,
            ]);
        }

        return view('admin.flash_deals.index',[
            'title'       => translate('Flash Deals'),
            'flash_deals' => FlashDeal::when(request()->input('name') ,function($q){
                                    return $q->where('name','like', '%'.request()->input('name').'%');
                                })->latest()->get()
        ]);
    }


    /**
     * Create Flash deals
     *
     * @return View
     */
    public function flashDealCreate() :View {

       

        return view('admin.flash_deals.create',[
            'title'       => translate('Create Flash Deal'),

        ]);
  
    }


    
    /**
     * Store a new flash deal 
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function flashDealStore(Request $request) :RedirectResponse {


        $request->validate([
            'name'         => ['required','max:191','unique:flash_deals,name'],
            'banner_image' => ['required','image',new FileExtentionCheckRule(file_format())],
            'start_date'   => ['required','before_or_equal:end_date'],
            'end_date'     => ['required','after_or_equal:start_date'],
            'products'     => ['required','array'],
            'products.*'   => ['required','exists:products,id'],
        ]);


        if($request->hasFile('banner_image')) {
            try {
                $banner = store_file($request->file('banner_image'), file_path()['flash_deal']['path']);
            }catch (\Exception $exp) {
            }
        }

        $flashDeal                  = new FlashDeal();
        $flashDeal->name            = $request->input('name');
        $flashDeal->slug            = make_slug($request->input('name'));
        $flashDeal->start_date      = $request->input('start_date');
        $flashDeal->end_date        = $request->input('end_date');
        $flashDeal->products        = $request->input('products',[]);
        $flashDeal->banner_image    = @$banner;
        $flashDeal->status          = 0;

        $flashDeal->save();

        return back()->with("success",translate("Flash Deal Created Successfully"));
    }



    /**
     * Edit Flash deals
     *
     * @param mixed $name
     * @return View
     */
    public function flashDealEdit(int | string  $id) :View{

        $flashDeal = FlashDeal::findOrfail($id);
        return view('admin.flash_deals.edit',[
            
            'title'       => translate('Edit Flash Deal'),
            'flashDeal'   => $flashDeal,
            'products'    => Product::whereIn('id',(array)@$flashDeal->products)
                                  ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                  ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                  ->get()

        ]);
    }

    /**
     * Update a  flash deal 
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function flashDealUpdate(Request $request) :RedirectResponse {


        $request->validate([
            'id'           => ['required','exists:flash_deals,id'],
            'name'         => ['required','max:191','unique:flash_deals,name,'.$request->input('id')],
            'banner_image' => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'start_date'   => ['required','before_or_equal:end_date'],
            'end_date'     => ['required','after_or_equal:start_date'],
            'products'     => ['required','array'],
            'products.*'   => ['required','exists:products,id'],
            
        ]);
        $flashDeal                  = FlashDeal::findOrfail($request->input('id'));

        $banner =  $flashDeal->banner_image;
        if($request->hasFile('banner_image')) {
            try {
                $banner = store_file($request->file('banner_image'), file_path()['flash_deal']['path'] ,null , $banner);
            }catch (\Exception $exp) {
            }
        }

        $flashDeal->name            = $request->input('name');
        $flashDeal->slug            = make_slug($request->input('name'));
        $flashDeal->start_date      = $request->input('start_date');
        $flashDeal->end_date        = $request->input('end_date');
        $flashDeal->products        = $request->input('products',[]);
        $flashDeal->banner_image    = @$banner;

        $flashDeal->save();

        return back()->with("success",translate("Flash Deal Updated Successfully"));

    }


    
    /**
     * Destroy a flash deal 
     *
     * @param int | string $id
     * @return RedirectResponse
     */
    public function flashDealDelete(int | string $id) :RedirectResponse {

        $flashDeal  = FlashDeal::findOrfail($id);
        $flashDeal->delete();
        return back()->with("success",translate("Flash Deal Deleted Successfully"));

    }



    /**
     * Destroy a flash deal 
     *
     * @param int | string $id
     * @return RedirectResponse
     */
    public function flashDealStatusUpdate(Request $request) :string {

        $request->validate([
            'data.id'=>'required|exists:flash_deals,id'
        ],[
            'data.id.required'=>translate('The Id Field Is Required')
        ]);
        $flashDeal         = FlashDeal::findOrfail($request->data['id']);
        $flashDeal->status =  $flashDeal->status == 1 ? 0 : 1;
        $flashDeal->save();
        
        if( $flashDeal->status == 1){
            FlashDeal::where('id','!=',$flashDeal->id)->update([
                'status' => 0
            ]);
        }


        $response['reload']  = true;
        $response['status']  = true;
        $response['message'] = translate('Flash Deal Status Updated Successfully');

        return json_encode([$response]);


    }








 
}
