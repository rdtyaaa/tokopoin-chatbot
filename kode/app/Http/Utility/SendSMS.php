<?php
namespace App\Http\Utility;
use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Models\GeneralSetting;
use App\Models\EmailTemplates;
use App\Models\SmsGateway;
use Textmagic\Services\TextmagicRestClient;
use Twilio\Rest\Client;

use GuzzleHttp\Client AS InfoClient;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;
use GuzzleHttp\Client as GazzleClient;
use Exception;
use Infobip\ApiException;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
class SendSMS
{
    public static function SMSNotification($userInfo, $templateSlug = null , $code = [] , $messages  = null )
    {


        $smsGateway = SmsGateway::where('status', '1')->where('id',site_settings('sms_gateway_id') )->first();
    
        if(!$smsGateway){
            return ;
        }
		
		if(!$messages){

			$template = EmailTemplates::where('slug', $templateSlug)->first();
			$messages      = str_replace("{{username}}", @$userInfo->username??@$userInfo->first_name , site_settings('sms_template'));
			$messages      = str_replace("{{message}}", @$template->sms_body, $messages);
	
			foreach ($code as $key => $value) {
				$messages = str_replace('{{' . $key . '}}', $value, $messages);
			}

		}
   


		
        $gatewayCode = [
            "101VON"        => "nexmo",
            "102TWI"        => "twilio",
            "103BIRD"       => "messageBird",
            "104INFO"       => "infobip",
        ];

        $response = '' ;
		if(isset($gatewayCode[$smsGateway->gateway_code])){
            return self::{$gatewayCode[$smsGateway->gateway_code]}($smsGateway->credential,  @$userInfo->phone, $messages);
        }

        return $response;
    }



    /**
	 * send nexmo sms
	 *
	 * @param object $credential
	 * @param mixed $to
	 * @param string $message
	 * @return string
	 */
	public static function nexmo(object $credential,mixed $to,string $message):string
	{


		$response = '' ;
		try {

			$basic  = new \Vonage\Client\Credentials\Basic($credential->api_key, $credential->api_secret);
			$client = new \Vonage\Client($basic);
			$response = $client->sms()->send(
				new \Vonage\SMS\Message\SMS( $to, $credential->sender_id,$message)
			);
		
			$message = $response->current();
		
			if($message->getStatus() != 0){
				$response = 'failed';
			}

		} 
		catch (\Exception $e){

			$response = 'failed';
	    }

		return $response;
		
	}

	/**
	 * send twilio sms
	 *
	 * @param object $credential
	 * @param string $to
	 * @param string $message
	 * @return string
	 */
	public static function twilio(object $credential,mixed $to,string $message):string
	{
		$response = '';
        try{
            $twilioNumber = $credential->from_number;
            $client = new Client($credential->account_sid, $credential->auth_token);
            $create = $client->messages->create('+'.$to, [
                'from' => $twilioNumber,
                'body' => $message
            ]);


        }catch (\Exception $e) {
		
			$response = 'failed';
        }

		return $response ;
	}


	/**
	 * send messageBird message
	 *
	 * @param object $credential
	 * @param string $to
	 * @param string $message
	 * @return string
	 */
	public static function messageBird(object $credential,mixed $to,string $message) :string
	{
	
		$response = '';
		try {
			$MessageBird 		 = new \MessageBird\Client($credential->access_key);
			$Message 			 = new \MessageBird\Objects\Message();
			$Message->originator = $credential->sender_id;
			$Message->recipients = array($to);
			$Message->body 		 = $message;
			$MessageBird->messages->create($Message);

		} catch (\Exception $e) {
			$response = 'failed';

		}

		return $response; 
	}


	/**
	 * send infobip message
	 *
	 * @param object $credential
	 * @param string $to
	 * @param string $message
	 * @return string
	 */
	public static function infobip(object $credential,string $to,string $message) :string
	{
		$response = '';
		try {
			$BASE_URL = $credential->infobip_base_url;
			$API_KEY = $credential->infobip_api_key;
			$SENDER = $credential->sender_id;
			$RECIPIENT =  preg_replace('/[^0-9]/', '', $to) ;
			$MESSAGE_TEXT = strip_tags($message);

			$configuration = (new Configuration())
				->setHost($BASE_URL)
				->setApiKeyPrefix('Authorization', 'App')
				->setApiKey('Authorization', $API_KEY);
			$client = new GazzleClient();
			$sendSmsApi = new SendSMSApi($client, $configuration);
			$destination = (new SmsDestination())->setTo($RECIPIENT);
			$message = (new SmsTextualMessage())
				->setFrom($SENDER)
				->setText($MESSAGE_TEXT)
				->setDestinations([$destination]);
			$request = (new SmsAdvancedTextualRequest())->setMessages([$message]);
			$smsResponse = $sendSmsApi->sendSmsMessage($request);

		} catch (\Exception $e) {
			$response = 'failed';
		}

		return 	$response;
	}


}