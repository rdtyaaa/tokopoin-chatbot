<?php


namespace App\Utilities;

use App\Enums\Settings\CacheKey;
use App\Enums\StatusEnum;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
use function response;
use Illuminate\Support\Facades\Cache;
class ApiJsonResponse
{
    protected int $httpCode = 200;
    protected int $code = 20000;
    protected string $message;
    
    protected mixed $data;
    protected ?string $details = '';
    protected array $headers = [];

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }


    /**
     * API success response
     *
     * @param string $message
     * @param integer  $httpCode
     * @param integer|null $code
     * @return JsonResponse
     */
    public function success(string $message = '', int $httpCode = Response::HTTP_OK, ?int $code = null): JsonResponse {

        $this->httpCode = $httpCode;

        /**
         * @var int $code
         */
        $this->code = $code ?? $httpCode * 100;
        $this->message = $message;

        $currency = Cache::get(CacheKey::API_CURRENCY->value);

        $response = [
            'status'    => 'SUCCESS',
            'code'      => $this->code,
            'message'   => translate($message),
            'locale'    => app()->getLocale(),
            'currency'  => new CurrencyResource( $currency),
            'default_currency'  => new CurrencyResource(default_currency()),
            'data'      => $this->data
        ];

        return response()->json($response, $this->httpCode, $this->headers);
    }


    /**
     * API failed response
     *
     * @param string $message
     * @param integer $httpCode
     * @param integer|null $code
     * @return JsonResponse
     */
    public function fails(string $message = '', int $httpCode = Response::HTTP_BAD_REQUEST, ?int $code = null): JsonResponse
    {

    
        $this->httpCode = $httpCode;

        /**
         * @var int $code
         */
        $this->code    = $code ?? $httpCode * 100;
        $this->message = $message;

    
        $response = [
            'status'  =>  'ERROR',
            'code'    =>  $this->code,
            'message' =>  translate($message),
            'locale'  =>  app()->getLocale(),
            'data'    =>  count($this->data) > 0 
                                            ?  $this->data 
                                            : (object)[]
           
        ];


        try {
            if(request()->is('api/seller/*')){
                $seller = auth()->guard('seller:api')->user() ;
                $response ['is_seller_authenticate'] =  $seller && $seller->status == 1
                                                                    ? true 
                                                                    : false;
            }



            if(request()->is('api/delivery_man/*')){
                $delivery_man = auth()->guard('delivery_man:api')->user() ;
                $response ['is_delivery_man_authenticate'] =  $delivery_man && $delivery_man->status == StatusEnum::true->status()
                                                                                    ? true 
                                                                                    : false;
          }



    
        } catch (\Throwable $th) {
          
        }
     
        return response()->json($response, $this->httpCode, $this->headers);
    }
}
