<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Http\Services\Admin\LanguageService;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public $languageService;
    public function __construct()
    {
        $this->languageService = new LanguageService();

        $this->middleware(['permissions:view_languages'])->only('index');
        $this->middleware(['permissions:create_languages'])->only('store');
        $this->middleware(['permissions:update_languages'])->only('tranlateKey','translate','statusUpdate','setDefaultLang');
        $this->middleware(['permissions:delete_languages'])->only('destroy','destroyTranslateKey');
    }
    
    /**
     * Get all language 
     *
     * @return View
     */
    public function index() :View
    {

        return view('admin.language.index', [
            'title'        => translate("Manage language"), 
            'languages'    => $this->languageService->index(),
            'countryCodes' => json_decode(file_get_contents(resource_path(config('constants.options.country_code')) . 'countries.json'),true)
        ]);
    }
  

    /**
     * Store  a language
     *
     * @param LanguageRequest $request
     * @return RedirectResponse
     */
    public function store(LanguageRequest $request) :RedirectResponse
    {
        $response = $this->languageService->store($request);
        return back()->with($response['status'],$response['message']);
    }

    
    /**
     * Set default language
     *
     * @param int | string $id 
     * @return RedirectResponse
     */
    public function setDefaultLang(int | string $id) :RedirectResponse{
        $response = $this->languageService->setDefault($id); 
        return back()->with($response['status'],$response['message']);
    }

    
    /**
     * Update language status
     *
     * @param Request $request
     * @return string
     */
    public function statusUpdate(Request $request) :string {

        $request->validate([
            'data.id'          => 'required|exists:languages,id'
        ],[
            'data.id.required'=> translate('The Id Field Is Required')
        ]);

        $language = Language::where('id',$request->data['id'])->first();
        $response['reload'] = true;

        if(session()->get('locale') == $language->code){
            $response['status'] = false;
            $response['message'] = translate('System Current Language Status Cant not be Updated');
        }
        else{
            if($language->is_default == '1'){
                $response['status'] = false;
                $response['message'] = translate('You Can not Update Default language Status');
            }
            else{
                $response = update_status($language->id,'Language',$request->data['status']);
                $response['reload'] = true;
            }
        }
        return json_encode([$response]);
    }

    
    /**
     * Translate a language
     *
     * @param string  $code
     * @return View
     */
    public function translate(string $code) :View{

        return view('admin.language.translate', [
            'title'        =>  translate("Translate language"), 
            'translations' =>  $this->languageService->translationVal($code)
        ]);
    }

    
    /**
     * Translate a specific key
     *
     * @param Request $request
     * @return string
     */
    public function tranlateKey(Request $request) :string {

        $response = $this->languageService->translateLang($request);

        return json_encode([
            "success" => $response
        ]);
    }

    
    /**
     * Destroy a specific language
     *
     * @param int |string  $id
     * @return RedirectResponse
     */
    public function destroy(int |string $id) :RedirectResponse
    {
        $response = $this->languageService->destory($id);
        return back()->with( $response['status'],$response['message']);
    }

   

    /**
     * Destroy a key 
     *
     * @param int |string $id $id
     * @return RedirectResponse
     */
    public function destroyTranslateKey(int |string $id) :RedirectResponse{
        $response = $this->languageService->destoryKey($id);
        return back()->with( $response['status'],$response['message']);
    }
}
