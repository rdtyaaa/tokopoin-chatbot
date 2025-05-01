<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\TicketReplyRequest;
use App\Http\Requests\Api\Seller\TicketStoreRequest;
use App\Http\Resources\Seller\TicketCollection;
use App\Http\Resources\Seller\TicketMessageCollection;
use App\Http\Resources\Seller\TicketResource;
use App\Http\Services\Deliveryman\TicketService as DeliverymanTicketService;
use App\Models\DeliveryMan;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SupportTicketController extends Controller
{
    protected ? DeliveryMan $deliveryman;
    public function __construct(protected DeliverymanTicketService $deliverymanTicketService){
        $this->middleware(function ($request, $next) {
            $this->deliveryman = auth()->guard('delivery_man:api')->user()?->load(['ratings','orders','refferedBy']);
            return $next($request);
        });
    }


    /**
     * Get Deliveryman ticket list 
     *
     * @return JsonResponse
     */
    public function list() : JsonResponse{


        return api([ 
            'tickets'                  => new TicketCollection($this->deliverymanTicketService->getTicketList($this->deliveryman))
        ])->success(__('response.success'));

    }


    /**
     * Get DeliveryMan ticket list 
     *
     * @return JsonResponse
     */
    public function ticketMessages(int | string $ticketNumber) : JsonResponse {


        $ticket  = $this->deliverymanTicketService->getTicketByNumber($this->deliveryman,$ticketNumber);

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

        $ticket  = $this->deliverymanTicketService->store($request ,$this->deliveryman)?->load(['messages','messages.supportfiles']);
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

        $ticket  = $this->deliverymanTicketService->getTicketByNumber($this->deliveryman, $request->input('ticket_number'));

        if(!$ticket) return api(['errors'=> [translate("Ticket not found")]])
                                     ->fails(__('response.fail'));

        if($ticket->status  == SupportTicket::CLOSED) return api(['errors'=> [translate("This ticket is closed")]])
        ->fails(__('response.fail'));
        
                                    
        $message  = $this->deliverymanTicketService->reply($request,$ticket);

        $ticket->status = 3;
        $ticket->save();

        return api([ 

            'ticket'                  => new TicketResource($ticket->load(['messages','messages.supportfiles'])),
            'ticket_messages'         => new TicketMessageCollection($ticket->messages),
        ])->success(__('response.success'));


    }



    /**
     * Close a ticket
     *
     * @return JsonResponse
     */
    public function close(int | string $ticketNumber) : JsonResponse {


        $ticket  = $this->deliverymanTicketService->getTicketByNumber($this->deliveryman,$ticketNumber);

        
        if(!$ticket) return api(['errors'=> [translate("Ticket not found")]])
                                     ->fails(__('response.fail'));


        if($ticket->status  == SupportTicket::CLOSED) return api(['errors'=> [translate("This ticket is already closed")]])
        ->fails(__('response.fail'));


        $ticket->status  = SupportTicket::CLOSED;
        $ticket->save();

        return api([ 

            'ticket'                  => new TicketResource($ticket),
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

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $ticket  = $this->deliverymanTicketService->getTicketByNumber($this->deliveryman, $request->input('ticket_number'));

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
