<?php

namespace App\Jobs;

use App\Http\Utility\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /* Create a new job instance.
    *
    * @return void
    */
   public $phone;
   public function __construct($phone)
   {
      $this->phone = $phone;


   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(){
       WhatsAppMessage::send($this->phone);
   }
}
