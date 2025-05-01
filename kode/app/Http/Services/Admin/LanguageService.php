<?php

namespace App\Http\Services\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class LanguageService extends Controller
{


    /**
     * Return language colletion
     *
     * @return Collection
     */
    public function index() :Collection
    {
        return Language::latest()->get();
    }



    /**
     * Store a specific language
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request) :array 
    {
        
        $country  =  explode("//", $request->name);
        $code     = $request->code?$request->code:  strtolower($country[1]);
        if(Language::where('code',$code)->exists()){
            $response['status']  = "error";
            $response['message'] = translate('This Language Is Already Added !! Try Another');
        }
        else{
            $language = Language::create([
                'name'      => $country[0],
                'code'      => $code,
                'is_default'=> (StatusEnum::false)->status(),
                'status'    => (StatusEnum::true)->status()
            ]);

            try {

                $translations         = Translation::where('code', 'en')->get();
                $translationsToCreate = [];
                
                foreach ($translations as $k) {

                    $translationsToCreate[] = [
                        'code'  => $language->code,
                        'key'   => $k->key,
                        'value' => $k->value
                    ];
                }
                
                Translation::insert($translationsToCreate);
    
 
            } catch (\Throwable $th) {
             
            }


            
            $response['status']  = "success";
            $response['message'] = translate('Language Created Succesfully');
            $response['data']    = $language;
        }
        return $response;
    }


    /**
     * Get all translation value
     *
     * @param string $code
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function translationVal(string $code) :\Illuminate\Pagination\LengthAwarePaginator
    {
        return Translation::where('code',$code)->paginate(site_settings('pagination_number',10));
    }



    /**
     * Translate a lang
     *
     * @param Request $request
     * @return boolean
     */
    public function translateLang(Request  $request) :bool{

        $response = true;
        try {
            Translation::where('id',$request->data['id'])->update([
                'value' => $request->data['value']
            ]);
            optimize_clear();
        } catch (\Throwable $th) {
            $response = false;
        }

        return $response;

    }


    /**
     * Set default language
     *
     * @param int | string $id
     * @return array
     */
    public function setDefault(int | string $id) :array {

        $response['status']  = "success";
        $response['message'] = translate('Default Language Set Successfully');
        Language::where('id','!=',$id)->update([
          'is_default'=> (StatusEnum::false)->status()
        ]);
        Language::where('id',$id)->update([
          'is_default'=>(StatusEnum::true)->status(),
        ]);
        return $response;
    }


    /**
     * Destroy a language
     *
     * @param int | string $id
     * @return array
     */
    public function destory(int | string $id) :array
    {
        $response['status']  = 'success';
        $response['message'] = translate('Deleted Successfully');
        try {
            $language = Language::where('id',$id)->first();
            if( $language->code == 'en' || $language->is_default == StatusEnum::true){
                $response['code']    = "error";
                $response['message'] = translate('Default & English Language Can Not Be Deleted');
            }
            else{

                Translation::where('code',$language->code)->delete();
                $language->delete();
            }
      
        } catch (\Throwable $th) {
            $response['status']  = 'error';
            $response['message'] = translate('Post Data Error !! Can Not Be Deleted');
        }
        return $response;
    }
    

    /**
     * Destroy a language key 
     *
     * @param int | string $id
     * @return array
     */
    public function destoryKey(int | string $id)
    {
        $response['status']  = 'success';
        $response['message'] = translate('Key Deleted Successfully');
        try {
            $transData = Translation::where('id',$id)->first();
            $transData->delete();
            optimize_clear();
      
        } catch (\Throwable $th) {
            $response['status']  = 'error';
            $response['message'] = translate('Post Data Error !! Can Not Be Deleted');
        }
        return $response;
    }

}
