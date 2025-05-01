<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\TicketReplyRequest;
use App\Http\Requests\Api\Seller\TicketStoreRequest;
use App\Http\Resources\Seller\TicketCollection;
use App\Http\Resources\Seller\TicketMessageCollection;
use App\Http\Resources\Seller\TicketResource;
use App\Http\Services\Seller\TicketService;
use App\Models\Seller;
use App\Models\SupportFile;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SupportTicketController extends Controller
{
    protected ? Seller $seller;

    public function __construct(protected TicketService $ticketService){
        $this->middleware(function ($request, $next) {
            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);
            return $next($request);
        });
    }




    /**
     * Get seller ticket list 
     *
     * @return JsonResponse
     */
    public function list() : JsonResponse {

        return api([ 
            'tickets'                  => new TicketCollection($this->ticketService->getTicketList($this->seller))
        ])->success(__('response.success'));

    }


    /**
     * Get seller ticket list 
     *
     * @return JsonResponse
     */
    public function ticketMessages(int | string $ticketNumber) : JsonResponse {


        $ticket  = $this->ticketService->getTicketByNumber($this->seller,$ticketNumber);

        if(!$ticket) return api(['errors'=> [translate("Ticket not found")]])
                                     ->fails(__('response.fail'));

        
        return api([ 

            'ticket'                  => new TicketResource($ticket),
            'ticket_messages'         => new TicketMessageCollection($ticket->messages),
        ])->success(__('response.success'));

    }



    /**
     * Store a new ticket
     *
     * @return JsonResponse
     */
    public function store(TicketStoreRequest $request) : JsonResponse {

        $ticket  = $this->ticketService->store($request ,$this->seller)?->load(['messages','messages.supportfiles']);
        return api([ 

            'ticket'                  => new TicketResource($ticket),
            'ticket_messages'         => new TicketMessageCollection($ticket->messages),
        ])->success(__('response.success'));


    }



    /**
     * Reply to a new ticket
     *
     * @return JsonResponse
     */
    public function reply(TicketReplyRequest $request) : JsonResponse {

        $ticket  = $this->ticketService->getTicketByNumber($this->seller, $request->input('ticket_number'));

        if(!$ticket) return api(['errors'=> [translate("Ticket not found")]])
                                     ->fails(__('response.fail'));

        if($ticket->status  == SupportTicket::CLOSED) return api(['errors'=> [translate("This ticket is closed")]])
        ->fails(__('response.fail'));
        

                                    
        $message  = $this->ticketService->reply($request,$ticket);

        $ticket->status = 3;
        $ticket->save();

        return api([ 

            'ticket'                  => new TicketResource($ticket->load(['messages','messages.supportfiles'])),
            'ticket_messages'         => new TicketMessageCollection($ticket->messages),
        ])->success(__('response.success'));


    }



    /**
     * Download support file
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function download(Request $request) : JsonResponse {



        $validator = Validator::make($request->all(),[
            'id'                        => 'required|exists:support_files,id',
            'support_message_id'        => 'required|exists:support_messages,id',
            'ticket_number'             => 'required|exists:support_tickets,ticket_number',
        ]);

        if ($validator->fails()){
            return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        }


        $ticket  = $this->ticketService->getTicketByNumber($this->seller, $request->input('ticket_number'));

        if(!$ticket) return api(['errors'=> [translate("Ticket not found")]])
                                     ->fails(__('response.fail'));


        $message = @$ticket->messages?->where('id',$request->input("support_message_id"))->first();                          

      
        if(!$message) return api(['errors'=> [translate("Invalid payload")]])
                                     ->fails(__('response.fail'));


        $file = @$message->supportfiles?->where('id',$request->input("id"))->first();



        if(!$file ) return api(['errors'=> [translate("File not found")]])
        ->fails(__('response.fail'));

        $file        = @$file->file;

        return api(
            [
                'url'   => @show_image(file_path()['ticket']['path'].'/'.$file)
            ])->success(__('response.success'));


    }








}
