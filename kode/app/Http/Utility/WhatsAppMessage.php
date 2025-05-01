<?php
namespace App\Http\Utility;

use App\Enums\Settings\CacheKey;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WhatsAppMessage
{
    public static $facebookAPI = 'https://graph.facebook.com/v18.0/';



   

    /**
     * Send whatsapp message
     * 
     * @param int | string $phone 
     * @param string $message
     * 
     * @return void
     */
   public static function send(int | string $phone,array $components  ) :void{

        $template = collect(json_decode(site_settings('wp_templates'),true))
                                    ->where('name',site_settings('wp_template'))
                                    ->first();
         
        try {


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . site_settings('wp_access_token'),
                'Content-Type' => 'application/json',
            ])->post(self::$facebookAPI.site_settings('wp_business_phone').'/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $phone , 
                'type' => 'template',
                'template'=>[
                    "name"=>   $template['name'],
                    "language"=> [
                        "code"=> $template['language']
                    ],
                    "components"=>  $components
                ]
            ]);

            $content = json_decode($response->body(),true);



        } catch (\Exception $e) {




        }

    

   }


   
   public static function loadTemplatesFromWhatsApp(){


    try {
        $url =  self::$facebookAPI.site_settings('wp_business_account_id').'/message_templates';

        $queryParams = [
            'fields' => 'name,category,language,quality_score,components,status',
            'limit' => 100
        ];
    
        $headers = [
            'Authorization' => 'Bearer '. site_settings('wp_access_token')
        ];
    
        $response = Http::withHeaders($headers)->get($url, $queryParams);
    
        if ($response->successful()) {
            $responseData = $response->json();
    
            $templates =  [];
            foreach ($responseData['data'] as $key => $template) {
    
                try {

                    if($template['status'] == 'APPROVED'){
                        $templates [] =[
                            'name'       => $template['name'],
                            'category'   => $template['category'],
                            'language'   => $template['language'],
                            'status'     => $template['status'],
                            'id'         => $template['id'],
                            'components' => ($template['components']),
                        ];
                    }

                    
                } catch (\Throwable $th) {
        
                }
            }
    
    
            Setting::updateOrInsert([
                'key' =>  'wp_templates',
            ],  ['value'  =>  json_encode( $templates)]);
    
            return [
                'status'  => true,
            ];
    
        }

        return [
            'status'   => false,
            'message'  => @$response->json()['error']['message'],
        ];
    } catch (\Exception $ex) {
        return [
            'status'  => false,
            'message' => $ex->getMessage(),
        ];
    }


    

}

}