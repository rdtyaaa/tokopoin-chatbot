<?php

use App\Enums\RewardPointStatus;
use App\Enums\Settings\CacheKey;
use App\Enums\StatusEnum;
use App\Models\Attribute;
use App\Models\Brand;
use Carbon\Carbon;
use App\Models\Currency;
use App\Models\DeliveryManRating;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;



if (!function_exists('site_settings')) {


    /**
     * Get Site Settings
     *
     * @param string|null $key
     * @param mixed $default
     * @return int | string | null
     */
    function site_settings(?string  $key,  mixed $default = null): int | string | array | object| null
    {

        return Arr::get(Cache::remember(CacheKey::SITE_SETTINGS->value, 24 * 60, function () {
            return Setting::pluck("value", 'key')->toArray();
        }), $key,  $default);
    }
}


if (!function_exists('api')) {
    function api($data = []): \App\Utilities\ApiJsonResponse
    {
        return new \App\Utilities\ApiJsonResponse($data);
    }
}

if (!function_exists('frontend_section_data')) {
    function frontend_section_data($data, $type, $key = 'value')
    {
        $val = '';
        $data = json_decode($data, true);
        if (isset($data[$type])) {
            $val = $data[$type][$key];
        }
        return $val;
    }
}

//show uploaded image
if (!function_exists('show_image')) {
    function show_image($image, $size = null)
    {
        $file = (asset('assets/images/default.jpg'));
        if (file_exists($image) && is_file($image)) {
            $file = asset($image);
        } elseif ($size) {
            $file  =  route('default.image', $size);
        }
        return $file;
    }
}


//store file method
if (!function_exists('store_file')) {
    function store_file($file, $location, $size = null, $removefile = null)
    {
        if (!file_exists($location)) {
            mkdir($location, 0755, true);
        }
        if ($removefile) {
            if (file_exists($location . '/' . $removefile) && is_file($location . '/' . $removefile)) {
                @unlink($location . '/' . $removefile);
            }
        }
        $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
        $image = Image::make(file_get_contents($file));
        if (isset($size)) {
            $size = explode('x', strtolower($size));
            $image->resize($size[0], $size[1]);
        }
        $image->save($location . '/' . $filename);
        return $filename;
    }
}

//remove a file
if (!function_exists('remove_file')) {
    function remove_file($location, $removefile)
    {
        if (file_exists($location)) {
            if (file_exists($location . '/' . $removefile) && is_file($location . '/' . $removefile)) {
                @unlink($location . '/' . $removefile);
            }
        }
    }
}
//active frontend menu
if (!function_exists('menu_active')) {
    function menu_active($url)
    {
        if ($url != url('/')) {
            if (url($url) ==  url()->current()) {
                return "navLink_active";
            }
        }
    }
}


//build post fields
if (!function_exists('build_post_fields')) {
    function build_post_fields($data, $existingKeys = '', &$returnArray = [])
    {
        if (($data instanceof CURLFile) or !(is_array($data) or is_object($data))) {
            $returnArray[$existingKeys] = $data;
            return $returnArray;
        } else {
            foreach ($data as $key => $item) {
                build_post_fields($item, $existingKeys ? $existingKeys . "[$key]" : $key, $returnArray);
            }
            return $returnArray;
        }
    }
}

//auth user informations
if (!function_exists('auth_user')) {
    function auth_user($guardName = 'admin')
    {
        return Auth::guard($guardName)->user();
    }
}

//make slug by string
if (!function_exists('make_slug')) {
    function make_slug($text)
    {
        return preg_replace('/\s+/u', '-', trim(strtolower($text)));
    }
}


//asset  file path
if (!function_exists('file_path')) {
    function file_path()
    {
        $path['profile'] = [
            'admin' => [
                'path' => 'assets/images/backend/profile',
                'size' => '150x150'
            ],
            'user' => [
                'path' => 'assets/images/frontend/profile',
                'size' => '150x150'
            ],
            'seller' => [
                'path' => 'assets/images/backend/seller/profile',
                'size' => '150x150'
            ],
            'delivery_man' => [
                'path' => 'assets/images/backend/delivery_man/profile',
                'size' => '150x150'
            ]
        ];
        $path['product'] = [
            'featured' => [
                'path' => 'assets/images/backend/product/featured',
                'size' => '800x650'
            ],

            'attribute' => [
                'path' => 'assets/images/backend/product/attribute'
            ],
            'gallery' => [
                'path' => 'assets/images/backend/product/gallery',
                'size' => '800x650'
            ]
        ];

        $path['digital_product'] = [
            'featured' => [
                'path' => 'assets/images/backend/product/featured',
                'size' => '400x250'
            ],
        ];

        $path['frontend'] = [
            'path' => 'assets/images/frontend',
        ];
        $path['onboarding_image'] = [
            'path' => 'assets/images/app',
            'size' => '400x400',
        ];


        $path['testimonial'] = [
            'path' => 'assets/images/testimonial',
            'size' => '150x150'
        ];


        $path['category'] = [
            'path' => 'assets/images/category',
            'size' => '200x200'
        ];

        $path['invoiceLogo'] = [
            'path' => 'assets/images/backend/invoiceLogo',
            'size' => '196x196'
        ];
        $path['newsLatter'] = [
            'path' => 'assets/images/global/newsLatter',
            'size' => '450x500'
        ];
        $path['blog'] = [
            'path' => 'assets/images/global/blog',
            'size' => '1000x375'
        ];
        $path['withdraw'] = [
            'path' => 'assets/images/backend/withdraw',
            'size' => '350x200'
        ];
        $path['delivery_man_kyc'] = [
            'path' => 'assets/images/backend/delivery_man_kyc',
        ];
        $path['seo_image'] = [
            'path' => 'assets/images/backend/seo',

            'size' => '600x600'
        ];
        $path['brand'] = [
            'path' => 'assets/images/backend/brand',
            'size' => '220x220'
        ];
        $path['shop_logo'] = [
            'path' => 'assets/images/shoplogo',
            'size' => '220x220'
        ];
        $path['seller_site_logo'] = [
            'path' => 'assets/images/sellerSiteLogo',
            'size' => '200x35'
        ];
        $path['seller_site_logo_sm'] = [
            'path' => 'assets/images/sellerSiteLogo',
            'size' => '80x80'
        ];
        $path['shop_first_image'] = [
            'path' => 'assets/images/shop',
            'size' => '2800x700'
        ];

        $path['payment_method'] = [
            'path' => 'assets/images/backend/paymentmethod',
            'size' => '200x200'
        ];
        $path['todays_deal_image'] = [
            'path' => 'assets/images/todaysDeal',
            'size' => '285x438'
        ];
        $path['fature_product'] = [
            'path' => 'assets/images/featureProduct',
            'size' => '285x438'
        ];

        $path['site_logo'] = [
            'path' => 'assets/images/backend/logoIcon',
            'size' => '130x50',
        ];

        $path['loder_logo'] = [
            'path' => 'assets/images/backend/logoIcon',
            'size' => '200x200'
        ];
        $path['admin_site_logo'] = [
            'path' => 'assets/images/backend/AdminLogoIcon',
            'size' => '200x60'
        ];
        $path['ticket'] = [
            'path' => 'assets/file/backend/ticket',
        ];

        $path['seller_kyc'] = [
            'path' => 'assets/file/backend/sellerKyc',
        ];
        $path['chat'] = [
            'path' => 'assets/file/backend/chat',
        ];
        $path['attribute_value'] = [
            'path' => 'assets/file/backend/attributevalue',
        ];
        $path['favicon'] = [
            'size' => '128x128',
        ];
        $path['menu'] = [
            'path' => 'assets/images/menu',
            'size' => '100x100'
        ];
        $path['menu_banner'] = [
            'path' => 'assets/images/menu/banner',
            'size' => '1900x190'
        ];
        $path['language'] = [
            'path' => 'assets/images/language',
            'size' => '20x20'
        ];
        $path['shipping_method'] = [

            'path' => 'assets/images/backend/shipping_method',
            'size' => '45x20'
        ];
        $path['text_editor_file'] = [

            'path' => 'assets/images/backend/text_editor_file',
            'size' => '45x20'
        ];
        $path['banner_image'] = [
            'path' => 'assets/images/banner_image',
            'size' => '1190x600'
        ];

        $path['campaign_banner'] = [
            'path' => 'assets/images/backend/campaign_banner',
            'size' => '2800x525'
        ];
        $path['flash_deal'] = [
            'path' => 'assets/images/backend/flash_deal',
            'size' => '2800x525'
        ];
        return $path;
    }
}



//short amount
if (!function_exists('short_amount')) {
    function short_amount(mixed $amount, bool $showCurrency = true, bool $number_format = true)
    {

        $deciamlDigit = site_settings('digit_after_decimal', 2);

        $currency  = session()->get('web_currency');

        if ($currency) {
            $amount *= $currency->rate;
        }


        if ($number_format) {
            $formattedAmount =  number_format($amount, $deciamlDigit);
        } else {
            $formattedAmount =  round((float)$amount, $deciamlDigit);
        }


        if ($showCurrency) {

            switch (site_settings('currency_position', StatusEnum::true->status())) {
                case StatusEnum::true->status():
                    $formattedAmount = $currency->symbol . $formattedAmount;
                    break;
                case StatusEnum::false->status():
                    $formattedAmount = $formattedAmount . $currency->symbol;
                    break;
            }
        }

        return $formattedAmount;
    }
}


if (!function_exists('show_amount')) {
    function show_amount(mixed $amount, string $symbol = null)
    {

        $deciamlDigit = site_settings('digit_after_decimal', 2);

        $currency        = session()->get('web_currency');
        $formattedAmount =  number_format($amount, $deciamlDigit);
        
        switch (site_settings('currency_position', StatusEnum::true->status())) {
            case StatusEnum::true->status():
                $formattedAmount =  ($symbol ?? $currency->symbol) . $formattedAmount;
                break;
            case StatusEnum::false->status():
                $formattedAmount = $formattedAmount . ($symbol ?? $currency->symbol);
                break;
        }



        return $formattedAmount;
    }
}


if (!function_exists('convert_to_base')) {
    function convert_to_base($amount, $length = 2)
    {

        $currency  = session()->get('web_currency');

        $amountInUSD = $amount /  (float)$currency->rate;

        return round($amountInUSD);
    }
}



if (!function_exists('exchange_rate')) {


    function exchange_rate(mixed $currency): int | float
    {

        $base    = default_currency();
        $amount  = $base->rate;

        try {
            $baseCurrency = session()->get('web_currency') ??   $base;
            $exchangeRate = $baseCurrency->rate / ($currency ? $currency->rate : $baseCurrency->rate);
            $amount       = 1 / $exchangeRate;
        } catch (\Throwable $th) {
        }

        return  round($amount);
    }
}




if (!function_exists('default_currency_converter')) {
    function default_currency_converter(int | float $amount, $currency): int | float
    {


        $amountInUsd = $amount / $currency->rate;

        return $amountInUsd * default_currency()->rate;
    }
}

//short amount
if (!function_exists('upload_new_file')) {

    function upload_new_file($file, $location, $old = null)
    {
        if (!file_exists($location)) {
            mkdir($location, 0755, true);
        }
        if (!$location) throw new Exception('File could not been created.');
        if ($old) {
            if (file_exists($location . '/' . $old) && is_file($location . '/' . $old)) {
                @unlink($old . '/' . $old);
            }
        }
        $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
        $file->move($location, $filename);
        return $filename;
    }
}

function api_short_amount(int | float $amount, int $length = 2): int | float
{
    $currency = Cache::get(CacheKey::API_CURRENCY->value);
    if ($currency) $amount *= $currency->rate;
    $deciamlDigit    = site_settings('digit_after_decimal', 2);
    return round($amount, $deciamlDigit);
}


if (!function_exists('get_currency_symbol')) {
    function get_currency_symbol(): string
    {
        $currency = Cache::remember(CacheKey::CURRENCY->value, 24 * 60, function () {
            Currency::where(function (Builder $query) {
                return $query->where('id', @session()->get('web_currency')->id)
                    ->orwhere('default', StatusEnum::true->status());
            })->first();
        });
        return  $currency ? $currency->symbol : "$";
    }
}


if (!function_exists('get_currency_name')) {
    function get_currency_name(): string
    {
        $currency = Cache::remember(CacheKey::CURRENCY->value, 24 * 60, function () {
            Currency::where(function (Builder $query) {
                return $query->where('id', @session()->get('web_currency')->id)
                    ->orwhere('default', StatusEnum::true->status());
            })->first();
        });

        return  $currency ? $currency->name : "USD";
    }
}


if (!function_exists('default_currency')) {
    function default_currency(): Currency
    {
        $currency = Cache::remember(CacheKey::DEFAULT_CURRENCY->value, 24 * 60, function () {
            return Currency::where(function (Builder $query) {
                return $query->where('default', StatusEnum::true->status());
            })->first();
        });

        return  $currency;
    }
}




//show currency
if (!function_exists('show_currency')) {
    function show_currency()
    {
        return @session()->get('web_currency')->symbol ?? get_currency_symbol();
    }
}


//discount calculations
if (!function_exists('discount')) {
    function discount($total, $discount, $type = 0)
    {
        if ($type == 0) {
            $price = $total - $discount;
        } else {
            $discount_amount = $total * ($discount / 100);
            $price = $total - $discount_amount;
        }

        return $price;
    }
}

if (!function_exists('hexa_to_rgba')) {
    function hexa_to_rgba($code)
    {
        list($r, $g, $b) = sscanf($code, "#%02x%02x%02x");
        return  "$r,$g,$b";
    }
}



//dif for human by date
if (!function_exists('diff_for_humans')) {
    function diff_for_humans($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

// get date by format
if (!function_exists('get_date_time')) {
    function get_date_time($date, $format = 'Y-m-d h:i A')
    {
        return Carbon::parse($date)->translatedFormat($format);
    }
}


// get transaction number for payment
if (!function_exists('trx_number')) {
    function trx_number($length = 14)
    {
        return strtoupper(Str::random($length));
    }
}

// get rand numnber
if (!function_exists('random_number')) {
    function random_number()
    {
        return mt_rand(1, 10000000);
    }
}

if (!function_exists('build_dom_document')) {
    /**
     * Summary of buildDomDocument
     * @param mixed $text
     * @return string
     */
    function build_dom_document($text, $name = 'text_area')
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $text);
        libxml_use_internal_errors(false);
        $imageFile = $dom->getElementsByTagName('img');
        if ($imageFile) {
            foreach ($imageFile as $item => $image) {
                $data = $image->getAttribute('src');
                $check_b64_data = preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).base64,.*/", $data);
                if ($check_b64_data) {
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $imgeData = base64_decode($data);
                    $image_name = $name . time() . $item . '.png';
                    $save_path  = file_path()['text_editor_file']['path'];

                    try {
                        Image::make($imgeData)->save($save_path . '/' . $image_name);
                        $getpath = asset(file_path()['text_editor_file']['path'] . '/' . $image_name);

                        $image->removeAttribute('src');
                        $image->setAttribute('src', $getpath);
                    } catch (Exception $e) {
                    }
                }
            }
        }
        $html = $dom->saveHTML();
        $html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');
        return $html;
    }
}


// round a number
if (!function_exists('round_number')) {
    function round_number($amount, $length = 2)
    {
        $amount = round($amount, $length);
        return $amount;
    }
}


//frornternd section
if (!function_exists('frontend_section')) {
    function frontend_section($slug = null)
    {

        $frontends = Cache::remember(CacheKey::FRONTEND->value, 24 * 60, fn () =>   Frontend::get());

        if ($slug) {
            return  $frontends->where('slug', $slug)->first();
        }

        return $frontends;
    }
}


// text sorted method
if (!function_exists('text_sorted')) {
    function text_sorted($text)
    {
        return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
    }
}


// translate static text
if (!function_exists('translate')) {
    function translate($keyWord, $lang_code = null)
    {
        try {
            $lang_code = $lang_code ? $lang_code : App::getLocale();
            $lang_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($keyWord)));
            $translate_data = Cache::remember('translations-' . $lang_code, now()->addHour(), function () use ($lang_code) {
                return Translation::where('code', $lang_code)->pluck('value', 'key')->toArray();
            });
            if (!array_key_exists($lang_key, $translate_data)) {
                $translate_val = str_replace(array("\r", "\n", "\r\n"), "", $keyWord);
                Translation::create([
                    'code' => $lang_code,
                    'key'  => $lang_key,
                    'value' => $translate_val
                ]);
                $keyWord = $translate_val;
                Cache::forget('translations-' . $lang_code);
            } else {
                $keyWord = $translate_data[$lang_key];
            }
        } catch (\Throwable $th) {
        }

        return ucfirst($keyWord);
    }
}




//product review method
if (!function_exists('product_add_review')) {
    function product_add_review($productId, $userId) {
        $orders = Order::with(['orderDetails'])->where('customer_id', $userId)->get();
        $productIds = @$orders->pluck('orderDetails.*.product_id')->collapse()->all();
        return in_array($productId, @$productIds ?? []) ? true : false;
    }
}



//count product  method
if (!function_exists('count_product')) {
    function count_product($brand)
    {
        $brand = Brand::with(['product' => function ($q) {
            return $q->where('status', '1');
        }, 'product.review'])->where('status', '1')->where('id', $brand->id)->firstOrFail();
        return $brand->product->count();
    }
}

//get attribute name method
if (!function_exists('attribute_name')) {
    function attribute_name($attributeId)
    {
        return Attribute::where('id', $attributeId)->pluck('name')->first();
    }
}


//pagination number
if (!function_exists('paginate_number')) {
    function paginate_number($number = 20)
    {
        return $number;
    }
}

//get random token
if (!function_exists('rand_token')) {
    function rand_token($length = 10)
    {
        return Str::random($length);
    }
}


//get file format
if (!function_exists('file_format')) {
    function file_format($type = 'image')
    {
        $imageFormat = ['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif'];
        $fileFormat = ['pdf', 'doc', 'exel'];
        if ($type == 'image') {
            return $imageFormat;
        } else {
            return $fileFormat;
        }
    }
}

//update status
if (!function_exists('update_status')) {
    function update_status($id, $modelName, $status, $columName = 'status')
    {
        $response['reload'] = true;
        $response['status'] = false;
        $response['message'] = translate('Status Update Failed');
        try {
            $data =  app(config('constants.options.model_namespace') . $modelName)::where('id', $id)
                                                                                                  ->latest()
                                                                                                  ->first();
            $data->{$columName} = $status;
            $data->save();
            $response['status'] = true;
            $response['message'] = translate('Status Updated Successfully');
        } catch (\Throwable $th) {
        }
        return $response;
    }
}


//mark update status
if (!function_exists('mark_status_update')) {
    function mark_status_update($modelName, $status, $column, $ids)
    {
        app(config('constants.options.modelNamespace') . $modelName)::whereIn('id', $ids)->update([
            $column => $status
        ]);
    }
}


//get general settings
if (!function_exists('general_setting')) {
    function general_setting()
    {
        return GeneralSetting::first();
    }
}
//get general settings
if (!function_exists('optimize_clear')) {
    function optimize_clear()
    {
        Artisan::call('optimize:clear');
    }
}

//get unautorized message
if (!function_exists('unauthorized_message')) {
    function unauthorized_message($message = 'Unauthorized access')
    {
        return translate($message);
    }
}

//get system locale lang
if (!function_exists('get_system_locale')) {
    function get_system_locale()
    {
        return session()->has('locale') ?  session()->get('locale') : App::getLocale();
    }
}


//num sort
if (!function_exists('num_sort')) {
    function num_sort($num, $precision = 2)
    {
        $suffix = '';
        if ($num < 1000) {
            return $num;
        } else if ($num < 1000000) {

            $num_format = ($num / 1000);
            $suffix = 'K';
        } else {
            $num_format = ($num / 1000000);
            $suffix = 'M';
        }
        return number_format($num_format, decimals: $precision) . $suffix;
    }
}


//limit lines
if (!function_exists('limit_lines')) {
    function limit_lines($text, $limit)
    {
        $lines = explode("\n", $text);
        $limited_lines = array_slice($lines, 0, $limit);
        $limited_text = implode("\n", $limited_lines);
        if (count($lines) > $limit) {
            $limited_text .= "\n....";
        }
        return $limited_text;
    }
}

//limit words
if (!function_exists('limit_words')) {
    function limit_words($text, $limit)
    {
        $words = explode(" ", $text);
        $limited_words = array_slice($words, 0, $limit);
        $limited_text = implode(" ", $limited_words);
        if (count($words) > $limit) {
            $limited_text .= "...";
        }
        return $limited_text;
    }
}


//calculate days in year
if (!function_exists('days_in_year')) {
    function days_in_year()
    {
        $year = date("Y");
        $days = 0;
        for ($month = 1; $month <= 12; $month++) {
            $days = $days + days_in_month($month, $year);
        }
        return $days;
    }
}

//calculate days in year
if (!function_exists('days_in_month')) {
    function days_in_month($month, $year)
    {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
}

//sort array data by months
if (!function_exists('sort_by_month')) {
    function sort_by_month(array $data, array $keys = null): array
    {

        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $sortedArray = [];
        foreach ($months as $month) {
            $overView                = (Arr::get($data, $month, $keys ?? 0));
            $sortedArray[$month] =     $overView;
        }

        return $sortedArray;
    }
}

//replace sort code
if (!function_exists('replace_sort_code')) {
    function replace_sort_code($message, $name = null, $replaceableCode = [])
    {

        $string = $message;
        foreach ($replaceableCode  as $key => $value) {
            $string = str_replace($key, $value, $string);
        }
        return $string;
    }
}


//get translations
if (!function_exists('get_translation')) {
    function get_translation($data, $lang = null)
    {
        $lang      = $lang ? $lang : session()->get("locale");
        $lang_data = (array)@json_decode($data, true);

        $default   = Arr::get($lang_data, 'en', 'default');

        if (array_key_exists($lang, $lang_data ?? [])) {
            $transate = $lang_data[$lang];
        }

        return    $transate ?? $default;
    }
}


//permissions check method
if (!function_exists('permission_check')) {
    function permission_check($permission)
    {
        $permissions = format_permissions(json_decode(auth_user()->role->permissions, true));
        if (in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('response_status')) {
    function response_status(string $message = 'Sucessfully Completed', string $key = 'success'): array
    {
        return [
            $key =>  translate($message)
        ];
    }
}


if (!function_exists('validateModelStatus')) {
    function validateModelStatus(Request $request, array $modelInfo)
    {

        $rules = [
            'data.id' => ['required', 'exists:' . $modelInfo['table'] . "," . $modelInfo['key']],
            'data.status' => ['required', Rule::in($modelInfo['values'])]
        ];

        $request->validate($rules);
    }
}


//permisson formations
if (!function_exists('format_permissions')) {
    function format_permissions($permissions)
    {
        $permission_values = [];
        foreach ($permissions as $features) {
            foreach ($features as $feature) {
                $permission_values = array_merge($permission_values, $feature);
            }
        }
        return $permission_values;
    }
}

if (!function_exists('negative_value')) {
    /**
     * @param int|float $value
     * @param $float
     * @return int|float
     */
    function negative_value(int|float $value, $float = false): int|float
    {
        if ($float) {
            $value = (float) $value;
        }
        return 0 - abs($value);
    }
}


if (!function_exists('str_unique')) {

    /**
     * @param int $length
     * @return string
     */
    function str_unique(int $length = 16): string
    {
        $side = rand(0, 1);
        $salt = rand(0, 9);
        $len = $length - 1;
        $string = \Illuminate\Support\Str::random($len <= 0 ? 7 : $len);
        $separatorPos = (int) ceil($length / 4);
        $string = $side === 0 ? ($salt . $string) : ($string . $salt);
        $string = substr_replace($string, '-', $separatorPos, 0);

        return substr_replace($string, '-', negative_value($separatorPos), 0);
    }
}


//calculate discount
if (!function_exists('cal_discount')) {
    function cal_discount($discount, $price)
    {

        return  $price - (($price / 100) * $discount);
    }
}


if (!function_exists('show_ratings')) {

    function show_ratings($ratings)
    {
        $html = '';
        for ($i = 0; $i < 5; $i++) {
            if ($i < round($ratings)) {
                $html .= '<span><i class="fa-solid fa-star"></i></span>';
            } else {
                $html .= '<span><i class="fa-regular fa-star"></i></span>';
            }
        }
        return $html;
    }
}


if (!function_exists('order_payment_status')) {

    function order_payment_status(int | string $paymentStatus): string
    {

        $class  =  'success';
        $status =  'Paid';

        if ($paymentStatus == Order::UNPAID) {
            $class  =  'danger';
            $status =  'Unpaid';
        }

        return "<span class=\"badge badge-soft-$class\">$status</span>";
    }
}


if (!function_exists('order_status')) {

    function order_status(int | string $status): string
    {

        switch ($status) {
            case Order::PLACED:
                $badgeClass = 'badge-soft-primary';
                $text = 'Placed';
                break;

            case Order::CONFIRMED:
                $badgeClass = 'badge-soft-info';
                $text = 'Confirmed';
                break;

            case Order::PROCESSING:
                $badgeClass = 'badge-soft-secondary';
                $text = 'Processing';
                break;

            case Order::SHIPPED:
                $badgeClass = 'badge-soft-warning';
                $text = 'Shipped';
                break;

            case Order::DELIVERED:
                $badgeClass = 'badge-soft-success';
                $text = 'Delivered';
                break;

            case Order::CANCEL:
                $badgeClass = 'badge-soft-danger';
                $text = 'Cancel';
                break;
        }

        return  '<span class="badge ' . $badgeClass . '">' . translate($text) . '</span>';
    }
}



if (!function_exists('product_status')) {

    function product_status(int | string $status): string
    {

        $badgeClass = 'badge-soft-info';
        $text = 'New';
        switch ($status) {
            case 1:
                $badgeClass = 'badge-soft-success';
                $text = 'Published';
                break;

            case 2:
                $badgeClass = 'badge-soft-info';
                $text = 'Inactive';
                break;

            case 3:
                $badgeClass = 'badge-soft-danger';
                $text = 'Cancel';
                break;
        }

        return  '<span class="badge ' . $badgeClass . '">' . translate($text) . '</span>';
    }
}


if (!function_exists('translateable_locale')) {

    function translateable_locale(object  $languages): array
    {

        $localeArray = $languages->pluck('code')->toArray();
        usort($localeArray, function ($a, $b) {

            $systemLocale              = session()->get("locale");
            $systemLocaleIndex         = array_search($systemLocale, [$a, $b]);
            return $systemLocaleIndex === false ? 0 : ($systemLocaleIndex === 0 ? -1 : 1);
        });

        return $localeArray;
    }
}


if (!function_exists('getLanguagesArr')) {

    function getLanguagesArr(object  $languages): array
    {
        return $languages->pluck('name', 'code')->toArray();
    }
}


if (!function_exists('is_demo')) {
    function is_demo(): bool{
        return strtolower(env('APP_MODE')) == 'demo' ? true : false;
    }
}





if (!function_exists('get_real_ip')) {
    function get_real_ip(): string
    {

        $ip = $_SERVER["REMOTE_ADDR"];

        if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        }
        if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }

        return $ip;
    }
}




if (!function_exists('get_ip_info')) {
    function get_ip_info(): array
    {
        $ip = get_real_ip();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/xml.gp?ip=" . $ip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            $xml = false;
        } else {
            $xml = simplexml_load_string($response);
        }

        $country  = $xml ? (string)$xml->geoplugin_countryName : "";
        $city     = $xml ? (string)$xml->geoplugin_city : "";
        $code     = $xml ? (string)$xml->geoplugin_countryCode : "";
        $long     = $xml ? (string)$xml->geoplugin_longitude : "";
        $lat      = $xml ? (string)$xml->geoplugin_latitude : "";

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 10/i'     => 'Windows 10',
            '/windows nt 6.3/i'    => 'Windows 8.1',
            '/windows nt 6.2/i'    => 'Windows 8',
            '/windows nt 6.1/i'    => 'Windows 7',
            '/windows nt 6.0/i'    => 'Windows Vista',
            '/windows nt 5.2/i'    => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'    => 'Windows XP',
            '/windows xp/i'        => 'Windows XP',
            '/windows nt 5.0/i'    => 'Windows 2000',
            '/windows me/i'        => 'Windows ME',
            '/win98/i'             => 'Windows 98',
            '/win95/i'             => 'Windows 95',
            '/win16/i'             => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i'       => 'Mac OS 9',
            '/linux/i'             => 'Linux',
            '/ubuntu/i'            => 'Ubuntu',
            '/iphone/i'            => 'iPhone',
            '/ipod/i'              => 'iPod',
            '/ipad/i'              => 'iPad',
            '/android/i'           => 'Android',
            '/blackberry/i'        => 'BlackBerry',
            '/webos/i'             => 'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        $browser = "Unknown Browser";
        $browser_array = array(
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i'    => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        $data = [
            'country'     => $country,
            'city'        => $city,
            'code'        => $code,
            'long'        => $long,
            'lat'         => $lat,
            'os_platform' => $os_platform,
            'browser'     => $browser,
            'ip'          => $ip,
            'time'        => date('d-m-Y h:i:s A')
        ];

        return $data;
    }
}




if (!function_exists('k2t')) {
    function k2t(string $text): string
    {
        return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
    }
}

if (!function_exists('t2k')) {
    function t2k(string $text, ?string $replace = "_"): string
    {
        return strtolower(strip_tags(str_replace(' ', $replace, $text)));
    }
}



if (!function_exists('unslug')) {
    /**
     * Convert a slug back to a human-readable string.
     *
     * @param string $slug
     * @param string $delimiter
     * @return string
     */
    function unslug($slug, $delimiter = '-')
    {
        // Replace delimiter with spaces
        $string = str_replace($delimiter, ' ', $slug);
        
        // Capitalize the first letter of each word
        $string = ucwords($string);
        
        return $string;
    }
}


//update env method
if (!function_exists('update_env')) {
    function update_env(string $key, string $newValue): void
    {
        $path = base_path('.env');
        $envContent = file_get_contents($path);
        if (preg_match('/^' . preg_quote($key, '/') . '=/m', $envContent)) {
            $envContent = preg_replace('/^' . preg_quote($key, '/') . '.*/m', $key . '=' . $newValue, $envContent);
        } else {
            $envContent .= PHP_EOL . $key . '=' . $newValue . PHP_EOL;
        }
        file_put_contents($path, $envContent);
    }
}
//update env method
if (!function_exists('guest_checkout')) {
    function guest_checkout(): bool
    {

        return site_settings('guest_checkout') == StatusEnum::true->status() ?  true : false;
    }
}


if (!function_exists('get_ai_option')) {
    function get_ai_option(): array
    {


        return [

            'improve_it' => [
                'prompt' => "Improve the above message writing"
            ],
            'Grammer Correction' => [
                'prompt' => "Correct any grammatical mistake in the message"
            ],
            'make_it_more_detailed' => [

                'prompt' => "Make this message More Detailed"
            ],
            'simplyfy_it' => [
                'prompt' => "Simplyfy this message"
            ],
            'make_it_informative' => [
                'prompt' => "Make the message more informative"
            ],
            'fix_any_mistake' => [
                'prompt' => "Fix if there is any mistake in the message"
            ],
            'sound_fluent' => [
                'prompt' => "Make this message as it sound more fluent"
            ],
            'make_it_objective' => [
                'prompt' => "Make  this message more objective"
            ],
        ];
    }
}

if (!function_exists('get_ai_tone')) {
    function get_ai_tone(): array
    {


        return [

            'engaging' => [
                'display_name' => "Make It Engaging",
                'prompt'       => "Make the message content tone more engaging",

            ],
            'sound_formal' => [
                'display_name' => "Sound Formal",
                'prompt'       => "Make the message content tone more formal",
            ],
            'sound_casual' => [
                'display_name' => "Sound Casual",
                'prompt'       => "Make the message  content tone  sound more casual",
            ],
            'friendly' => [
                'display_name' => "Make It Friendly",
                'prompt'       => "Make the message content tone more user friendly",
            ],

            'exciting' => [
                'display_name' => "Make It Exciting",
                'prompt'       => "Make the message content tone more exciting",
            ],

            'confident' => [
                'display_name' => "Make It Confident",
                'prompt'       => "Make the message content tone more Confident",
            ],

            'assertive' => [
                'display_name' => "Make It Assertive",
                'prompt'       => "Make the message content tone more assertive",
            ],

        ];
    }
}


// get automatic active payment methods
if (!function_exists('active_payment_methods')) {
    function active_payment_methods(): Collection
    {

        return PaymentMethod::automatic()
                                ->active()
                                ->get();
    }
}


// get manual active payment methods
if (!function_exists('active_manual_payment_methods')) {
    function active_manual_payment_methods(): Collection
    {

        return PaymentMethod::manual()
                                ->active()
                                ->get();
    }
}


// get manual active payment methods
if (!function_exists('convertArrayToObject')) {
    function convertArrayToObject(array $array): object
    {
        return (object) $array;
    }
}


if (!function_exists('get_cached_attributes')) {
    function get_cached_attributes(): Collection
    {

        return  Cache::remember(CacheKey::PRODUCT_ATTRIBUTE->value, 24 * 60, fn () =>  \App\Models\Attribute::with(['value'])->select('id', 'name')->get());
    }
}


if (!function_exists('getTaxes')) {
    function getTaxes(mixed $product, $stockPrice, $summation = true): int|float|\Illuminate\Support\Collection
    {
        $collection = (@$product->taxes->map(function (\App\Models\Tax $tax) use ($stockPrice): int|float {
            $pivot = @$tax->pivot;
            if ($pivot->type == 0) return ((($stockPrice / 100) * $pivot->amount));
            return  @$tax->pivot->amount ?? 0;
        }));


        if ($summation) return $collection->sum();

        return $collection;
    }
}


if (!function_exists('getTaxesCollection')) {
    function getTaxesCollection(Product $product, $stockPrice, $summation = true): \Illuminate\Support\Collection
    {

        return (@$product->taxes->map(function (\App\Models\Tax $tax) use ($stockPrice): array {
            $pivot = @$tax->pivot;
            if ($pivot->type == 0) $price =   ((($stockPrice / 100) * $pivot->amount));
            $price =   @$tax->pivot->amount ?? 0;

            return [
                'name'         => $tax->name,
                'stock_price'  => $stockPrice,
                'price'        => $price,
                'type'         => $pivot->type == 0 ? 'percent' : "falt",
            ];
        }));
    }
}






if (!function_exists('calculateShippingCharge')) {
    function calculateShippingCharge($shippingDelivery, $items,    $zone)
    {

        $weight = 0;
        $price  = 0;

        foreach ($items as $item) {
            if ($item->product) {
                $weight += $item->product->weight * $item->quantity;
            }
            $price += $item->total;
        }

  

        $filterValue =  @$shippingDelivery->shipping_type == 'weight_wise' ? $weight : $price;


        if ($shippingDelivery->free_shipping ==  StatusEnum::true->status()) return 0;

        $configuration = collect($shippingDelivery->price_configuration)
            ->filter(function ($item) use ($filterValue, $zone) {
                return $item->zone_id == $zone->zone_id &&
                    $filterValue > $item->greater_than &&
                    $filterValue <= $item->less_than_eq;
            })->first();
        return  @$configuration->cost ?? 0;
    }
}



if (!function_exists('order_status_badge')) {
    function order_status_badge(int | string $status): string 
    {


        $order_status = translate('Pending');

        $class = 'badge badge-soft-primary';
        
        switch ($status) {
            case Order::PLACED:
                    $order_status = translate('Placed');
                    $class = 'badge badge-soft-primary';
                break;
            case Order::CONFIRMED:
                    $order_status = translate("Confirmed"); 
                    $class = 'badge badge-soft-info';
                break;
            case Order::PROCESSING:
                $order_status = translate("Processing");
                $class = 'badge badge-soft-secondary';
                break;
            case Order::SHIPPED:
                $order_status = translate("Shipped");
                $class = 'badge badge-soft-warning';
                break;
            case Order::DELIVERED:
                $order_status = translate("Delivered");
                $class = 'badge badge-soft-success';
                break;
            case Order::CANCEL:
                $order_status = translate("Cancel");
                $class = 'badge badge-soft-danger';
                break;
            case Order::PENDING:
                $class = 'badge badge-soft-primary';
                $order_status = translate("Pending");
                break;

            case Order::RETURN:
                $class = 'badge badge-soft-warning';
                $order_status = translate("Returned");
                break;
        }
        

      return  "<span class=\"badge $class\">$order_status</span>";



    }
}



if (!function_exists('reward_status')) {
    function reward_status(int | string $status): string 
    {

        switch ($status) {
            case RewardPointStatus::PENDING->value:
                    $order_status = translate('Pending');
                    $class = 'badge badge-soft-info';
                break;
            case RewardPointStatus::REDEEMED->value:
                    $order_status = translate("Redeemed"); 
                    $class = 'badge badge-soft-success';
                break;
        
            case RewardPointStatus::EXPIRED->value:
                    $order_status = translate("Expired");
                    $class = 'badge badge-soft-danger';
                break;
          
        }
        

      return  "<span class=\"badge $class\">$order_status</span>";



    }

}




if (!function_exists('getSeller')) {
    function getSeller(): Collection{
        return Seller::get();
    }

}




if (!function_exists('calculateDistance')) {
    function calculateDistance($formAddress, $toAddress)
    {
        $earthRadius = 6371; 

            if( $formAddress->latitude &&
                $formAddress->longitude && 
                $toAddress->latitude &&
                $toAddress->longitude 
            ){

            $lat1 = $formAddress->latitude;
            $lon1 = $formAddress->longitude;


            $lat2 = $toAddress->latitude;
            $lon2 = $toAddress->longitude;

            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
    
            $a = sin($dLat / 2) * sin($dLat / 2) +
                        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                        sin($dLon / 2) * sin($dLon / 2);
    
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
            $distance = $earthRadius * $c; 
    
            return round($distance,4);

           }

           return null;

 
    }
}


    if (!function_exists('distanceInWords')) {
        function distanceInWords($distance)
        {
            
            if ($distance < 1) {
                $meters = $distance * 1000;
                return round($meters) . ' m away';
            } elseif ($distance < 0.001) {
                $centimeters = $distance * 100000;
                return round($centimeters) . ' cm away';
            } else {
                return round($distance, 2) . ' km away';
            }
        }
    }

    if (!function_exists('generateOrderCode')) {
        function generateOrderCode($length = 6) {

        
            $otpCode = '';
            for ($i = 0; $i < $length; $i++) {
                $otpCode .= mt_rand(0, 9); 
            }
        
            while (Order::where('verification_code', $otpCode)->exists()) {
                $otpCode = generateOrderCode($length); 
            }
        
            return $otpCode;
        }
    }
    