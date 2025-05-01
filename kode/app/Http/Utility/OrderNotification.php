<?php
namespace App\Http\Utility;

use App\Enums\Settings\GlobalConfig;
use App\Enums\StatusEnum;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Jobs\SendWhatsAppJob;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\User;
use Carbon\Carbon;

class OrderNotification
{





    public function __construct(){

    }



    /**
     * Send Order placed notifications
     *
     * @param Order $order
     * @param User|null $user
     * @return void
     */
    public static function placed(Order $order , ? User $user = null , ? Currency $currency = null  ) :void {


        $order = $order->load(['orderDetails']);

        $tax      =  @$order->orderDetails->sum('total_taxes') ?? 0;
        $subTotal =  $order->amount - ( $order->discount +   $order->shipping_charge);
        $items    =  self::getItemVariable($order ,$currency);
        $originalPrice = @$order->orderDetails->sum('original_price');
  

        $first_name   = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
        $address      = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;
        $country      = @$order->billingAddress ? @$order->billingAddress->country->name : @$order->billing_information->country;
        $city         = @$order->billingAddress ? @$order->billingAddress->city->name : @$order->billing_information->city;
        $zip          = @$order->billingAddress ? @$order->billingAddress->zip : @$order->billing_information->zip;
        $state        = @$order->billingAddress ? @$order->billingAddress->state->name : @$order->billing_information->state;


        $templateCodes =   [

            '[order_no]'        =>  $order->order_id,
            '[customer_name]'   =>  @$first_name ?? "N/A",
            '[billing_address]' =>  @$address ?? 'N/A',
            '[billing_country]' =>  @$country ?? 'N/A',
            '[billing_city]'    =>  @$city ?? 'N/A',

            '[billing_zip_code]'=> @$zip ?? 'N/A',
            '[billing_state]'   => @$state ?? 'N/A',
            '[item_variable]'   => $items,
            '[sub_total]'       => show_amount($originalPrice  ,$currency?$currency->symbol : null),
            '[discount_amount]' => show_amount($order->discount ,$currency?$currency->symbol : null),
            '[shipping_amount]' => show_amount($order->shipping_charge ,$currency?$currency->symbol : null),
            '[tax_amount]'      => show_amount($tax ,$currency?$currency->symbol : null),
            '[final_total]'     => show_amount($order->amount ,$currency?$currency->symbol : null),
            '[link]'            => route('admin.inhouse.order.details', $order->id),
            '[time]'            => Carbon::now(),

        ];


        # SMS MESSAGE
        $message = str_replace(
            array_keys(GlobalConfig::ORDER_VARIABLE),
                       array_values( $templateCodes),
             site_settings('order_message')
        );





        if(site_settings('email_order_notification',StatusEnum::false->status()) == StatusEnum::true->status()){

                SendMailJob::dispatch(user :(object)[
                'email' => site_settings('mail_from')
                ],message : str_replace(
                array_keys(GlobalConfig::ORDER_VARIABLE),
                    array_values( $templateCodes),
                    site_settings('order_email_message')
            )
            ,
            subject :'Order placed'
            );
        }
        if (site_settings('sms_order_notification',StatusEnum::false->status()) == StatusEnum::true->status()){
                SendSmsJob::dispatch(user :(object)[
                    'phone' => site_settings('phone')
                ],message : $message,
            );
        }


        if ( site_settings('whatsapp_order_notification',StatusEnum::false->status()) == StatusEnum::true->status() ){


            try {
                $components = self::replaceVariables(
                    json_decode(site_settings('wp_notification_message_component'),true)
                    ,$templateCodes
                );
               (WhatsAppMessage::send(site_settings('wp_receiver_id'),$components));
            } catch (\Throwable $th) {

            }




        }


    }


    public static function replaceVariables($template, array $variablesValues) {

        foreach ($template as &$section) {
            foreach ($section['parameters'] as &$parameter) {
                foreach ($variablesValues as $variable => $value) {
                    $parameter['text'] = str_replace($variable, $value, $parameter['text']);
                }
            }
        }
        return $template;
    }





    /**
     * Get order items details for messaging templates
     *
     * @param Order $order
     * @param Currency|null $currency
     * @return string
     */
    public static  function getItemVariable(Order $order , ? Currency $currency = null) : string {


       return  OrderDetails::with(['product'])
                         ->where('order_id',$order->id)
                         ->lazyById(100,'id')
                         ->map(fn(OrderDetails $item) =>
                              str_replace(
                                array_keys(GlobalConfig::ITEM_VARIABLE),
                                            [
                                                $item->product->name,
                                                $item->attribute,
                                                $item->quantity,
                                                show_amount($item->original_price ,$currency?$currency->symbol : null),
                                            ],
                                 site_settings('item_variable','{quantity} x {product_name} - {variant_name}  = {item_total}')
                                )
                    );


    }

}
