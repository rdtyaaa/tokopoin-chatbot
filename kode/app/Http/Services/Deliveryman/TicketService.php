<?php

namespace App\Http\Services\Deliveryman;


use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\SupportFile;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class TicketService extends Controller
{





    /**
     * Get deliveryman tickets
     *
     * @param DeliveryMan $deliveryMan
     * @return LengthAwarePaginator
     */
    public function getTicketList(DeliveryMan $deliveryMan) :LengthAwarePaginator{

        return SupportTicket::search()->with(['deliveryMan'])
                                     ->whereNotNull('deliveryman_id')
                                     ->where('deliveryman_id', $deliveryMan->id)
                                     ->latest()->paginate(site_settings('pagination_number',10));


    }


    /**
     * Get a specific ticket by ticket number
     *
     * @param DeliveryMan $deliveryMan
     * @param int | string $ticketNumber
     * @return SupportTicket | null 
     */
    public function getTicketByNumber(DeliveryMan $deliveryMan , int | string $ticketNumber) : SupportTicket | null {

        return SupportTicket::with(['messages','messages.supportfiles'])
                                    ->whereNotNull('deliveryman_id')
                                    ->where('deliveryman_id', $deliveryMan->id)
                                     ->where("ticket_number",$ticketNumber)
                                     ->first();


    }



    /**
     * Store a new ticket
     *
     * @param Request $request
     * @param DeliveryMan $deliveryMan
     * @return SupportTicket
     */
    public function store(Request $request , DeliveryMan $deliveryMan)  : SupportTicket {

        $supportTicket                 = new SupportTicket();
        $supportTicket->ticket_number  = random_number();
        $supportTicket->deliveryman_id      = $deliveryMan->id;
        $supportTicket->subject        = $request->input("subject");
        $supportTicket->priority       = $request->input("priority");
        $supportTicket->status = 1;
        $supportTicket->save();
        $message  = $this->reply($request ,$supportTicket);
        return $supportTicket;


    }



    /**
     * Store reply for a specific ticket
     *
     * @param Request $request
     * @param SupportTicket $ticket
     * @return SupportMessage
     */
     public function reply(Request $request  ,SupportTicket $ticket ) : SupportMessage {

        
        $message                     = new SupportMessage();
        $message->support_ticket_id  = $ticket->id;
        $message->admin_id           = null;
        $message->message            = $request->input('message');
        $message->save();

        if($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                try {
                    $supportFile                     = new SupportFile();
                    $supportFile->support_message_id = $message->id;
                    $supportFile->file               = upload_new_file($file, file_path()['ticket']['path']);
                    $supportFile->save();
                } catch (\Exception $exp) {
                   
                }
            }
        }

        return  $message ;

     }




}