<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Utility\SendMail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user,$template,$code ,$message ,$subject;
    public function __construct($user,$template = null, $code = [] ,$message =  null ,$subject =  null)
    {
       $this->user = $user;
       $this->template = $template;
       $this->code = $code;
       $this->message = $message;
       $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SendMail::MailNotification($this->user,$this->template,$this->code ,$this->message ,$this->subject );
    }
}
