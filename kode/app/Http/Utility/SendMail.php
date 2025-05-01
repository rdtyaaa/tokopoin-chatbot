<?php
namespace App\Http\Utility;
use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Models\GeneralSetting;
use App\Models\EmailTemplates;

class SendMail
{
    public static function MailNotification($userInfo, $emailTemplate = null, $code = [] ,$messages  = null , $subject =  null)
    {



        $mailConfiguration = MailConfiguration::where('status', '1')->where('id',site_settings('email_gateway_id'))->first();
    
        if(!$mailConfiguration){
            return ;
        }

        $emailTemplate = EmailTemplates::where('slug', $emailTemplate)->first();


        if(!$messages ){

            $messages = str_replace("{{username}}", @$userInfo->username??@$userInfo->first_name , site_settings('email_template') );
            $messages = str_replace("{{message}}", @$emailTemplate->body, $messages);
            foreach ($code as $key => $value) {
                $messages = str_replace('{{' . $key . '}}', $value, $messages);
            }
    
        }
  
        $subject = $subject ?? $emailTemplate->subject;
        
        $response = '' ;

        if($mailConfiguration->name === "PHP MAIL"){
            $response = self::SendPHPmail(site_settings('mail_from'), $userInfo->email,$subject, $messages);
        }elseif($mailConfiguration->name === "SMTP"){
            $response = self::SendSMTPMail($mailConfiguration->driver_information->from->address, $userInfo->email,$subject, $messages);
        }elseif($mailConfiguration->name === "SendGrid Api"){
            $response = self::SendGrid(site_settings('mail_from') , $userInfo->email, @$userInfo->name,$subject, $messages, @$mailConfiguration->driver_information->app_key);
        }
        return $response;
    }

    public static function SendSMTPMail($emailFrom, $emailTo, $subject, $messages)
    {
        $response ='';
        try{
            Mail::send([], [], function ($message) use ($messages, $emailFrom, $emailTo, $subject ) {

                    $message->to($emailTo) 
                    ->subject($subject)
                    ->from($emailFrom,site_settings('site_name'))
                    ->setBody($messages, 'text/html','utf-8');
                
            });
        }
        catch(\Exception $exception){
            $response ='failed';
        }
        return $response;
 
    }

    public static function SendPHPmail($emailFrom, $emailTo, $subject, $messages)
    {
        $headers = "From: <$emailFrom> \r\n";
        $headers .= "Reply-To: $emailTo \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        @mail($emailTo, $subject, $messages, $headers);
        return '';
    }

    public static function SendGrid($emailFrom, $emailTo, $receiverName, $subject, $messages, $credentials)
    {
        $response ='';

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($emailFrom, site_settings('site_name'));
        $email->setSubject($subject);
        $email->addTo($emailTo, $receiverName);
        $email->addContent("text/html", $messages);
        $sendgrid = new \SendGrid($credentials);
        try {
            $response = $sendgrid->send($email);
        } catch (\Exception $e) {
            $response ='failed';
        }
        return $response;
    }
}