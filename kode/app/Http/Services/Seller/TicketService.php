<?php

namespace App\Http\Services\Seller;


use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SupportFile;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class TicketService extends Controller
{





    /**
     * Get seller tickets
     *
     * @param Seller $seller
     * @return LengthAwarePaginator
     */
    public function getTicketList(Seller $seller) :LengthAwarePaginator{

        return SupportTicket::search()->with(['seller'])
                                     ->whereNotNull('seller_id')
                                     ->where('seller_id', $seller->id)
                                     ->latest()->paginate(site_settings('pagination_number',10));


    }


    /**
     * Get a specific ticket by ticket number
     *
     * @param Seller $seller
     * @param int | string $ticketNumber
     * @return SupportTicket | null 
     */
    public function getTicketByNumber(Seller $seller , int | string $ticketNumber) : SupportTicket | null {

        return SupportTicket::with(['messages','messages.supportfiles'])
                                     ->whereNotNull('seller_id')
                                     ->where('seller_id', $seller->id)
                                     ->where("ticket_number",$ticketNumber)
                                     ->first();


    }



    /**
     * Store a new ticket
     *
     * @param Request $request
     * @param Seller $seller
     * @return SupportTicket
     */
    public function store(Request $request , Seller $seller)  : SupportTicket {

        $supportTicket                 = new SupportTicket();
        $supportTicket->ticket_number  = random_number();
        $supportTicket->seller_id      = $seller->id;
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