<?php

namespace App\Jobs;

use App\Http\Utility\SendMail;
use App\Http\Utility\SendSMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user,$template,$code , $message;
    public function __construct($user,$template = null,$code =  null , $message =  null)
    {
       $this->user     = $user;
       $this->template = $template;
       $this->code     = $code;
       $this->message  = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SendSMS::SMSNotification($this->user,$this->template,$this->code ,$this->message);
    }
}
