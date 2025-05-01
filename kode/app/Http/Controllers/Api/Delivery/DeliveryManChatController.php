<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Enums\Settings\NotificationType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deliveryman\DeliveryManChatCollection;
use App\Http\Resources\Deliveryman\DeliveryManCollection;
use App\Http\Resources\Deliveryman\DeliveryManResource;
use App\Http\Resources\DeliverymanConversationCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Services\Deliveryman\DeliverymanService;
use App\Models\CustomerDeliverymanConversation;
use App\Models\DeliveryMan;
use App\Models\DeliveryManConversation;
use App\Models\User;
use App\Rules\General\FileExtentionCheckRule;
use App\Traits\Notify;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryManChatController extends Controller
{


    use Notify;
    protected ? DeliveryMan $deliveryman;

    public function __construct(protected DeliverymanService $deliverymanService){
        $this->middleware(function ($request, $next) {
            $this->deliveryman = auth()->guard('delivery_man:api')->user()?->load(['ratings','orders','refferedBy']);


            if(site_settings('chat_with_deliveryman') != StatusEnum::true->status()) return api(['errors' => [translate('Chat with deliveryman currently off.')]])->fails(__('response.error'));

            return $next($request);
        });
    }

    




    public function list ():JsonResponse {


        $deliverymanIds = DeliveryManConversation::latest()
                            ->where(fn(Builder $query) :Builder  => $query->where('sender_id', $this->deliveryman->id)
                                                                    ->orWhere('receiver_id', $this->deliveryman->id))
                            ->get(['sender_id', 'receiver_id'])
                            ->map(function(DeliveryManConversation $conversation) {
                                if ($conversation->sender_id != $this->deliveryman->id) {
                                    return $conversation->sender_id;
                                } elseif ($conversation->receiver_id != $this->deliveryman->id) {
                                    return $conversation->receiver_id;
                                }
                            })
                            ->filter() 
                            ->unique() 
                            ->values() 
                            ->toArray();
        

        $deliverymens = DeliveryMan::with(['latestSenderMessage','latestReceiverMessage'])->whereIn('id',$deliverymanIds)
                            ->lazyById(100 ,'id')
                            ->map(function(DeliveryMan $deliveryMan){
                                $receiverMessage = @$deliveryMan?->latestSenderMessage;
                                $senderMessage   = @$deliveryMan?->latestReceiverMessage;
                                $latestMessage = null;
                                if ($senderMessage && $receiverMessage) {
                                    $latestMessage = $senderMessage->created_at > $receiverMessage->created_at 
                                                         ?$senderMessage : $receiverMessage;
                                } elseif ($senderMessage) {
                                    $latestMessage = $senderMessage;
                                } elseif ($receiverMessage) {
                                    $latestMessage = $receiverMessage;
                                }
                                if($latestMessage) $deliveryMan->latest_deliveryman_message =  $latestMessage;
                                return $deliveryMan;
                            })->all();






        return api([ 
            'delivery_men'                  => new DeliveryManCollection($deliverymens)
        ])->success(__('response.success'));

    }
  


    public function getChat ($deliverman_id):JsonResponse {


        $deliveryMan = DeliveryMan::where('id',$deliverman_id)
                                            ->first();

        
                                        
        if(!$deliveryMan)  return api(['errors'=> [translate("Invalid deliveryman")]])->fails(__('response.fail'));



        DeliveryManConversation::where('receiver_id',$this->deliveryman->id)
                                   ->where('sender_id',$deliveryMan->id)
                                   ->lazyById(100,'id')
                                   ->each
                                   ->update(['is_seen' => 1]);


        $messages = DeliveryManConversation::latest()->where(fn(Builder $query) : Builder => 
                                               $query->where('sender_id',$deliveryMan->id)
                                                           ->where('receiver_id',$this->deliveryman->id)
                                            )
                                            ->orWhere(fn(Builder $query) : Builder => 
                                                 $query->where('sender_id',$this->deliveryman->id)
                                                             ->where('receiver_id',$deliveryMan->id)
                                              )
                                            ->paginate(paginate_number());
                                
                   

        return api([ 
            'deliveryman'             => new DeliveryManResource($deliveryMan),
            'logged_in_deliveryman'   => new DeliveryManResource($this->deliveryman),
            'messages'                => new DeliveryManChatCollection($messages),
        ])->success(__('response.success'));

    }




    /**
     * Reply to a new ticket
     *
     * @return JsonResponse
     */
    public function sendMessage(Request $request) : JsonResponse {

        $request->validate([
            'delivery_man_id' => 'required|exists:delivery_men,id',
            'message' => 'required|max:191',
            'files.*'  => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')] 
        ]);


        $deliveryMan = DeliveryMan::where('id','!=',$this->deliveryman->id)
                                      ->where('id',$request->input('delivery_man_id'))
                                      ->first();


        if(!$deliveryMan)  return api(['errors'=> [ translate('Invalid payload')]])->fails(__('response.fail'));


        
        $message                     = new DeliveryManConversation();
        $message->message            = $request->input('message');
        $message->sender_id          = $this->deliveryman->id ;
        $message->receiver_id        = $deliveryMan->id;
        $message->is_seen            = 0;


        if($request->hasFile('files')) {
            $files   =  [];
            foreach ($request->file('files') as $file) {
     
                try {
                    $files []     = upload_new_file($file, file_path()['chat']['path']);
                } catch (\Exception $exp) {
                   
                }
            }

            $message->files =         $files;
        }


        $message->save();


        #FIREBASE NOTIFICATIONS
        if($deliveryMan &&  $deliveryMan->fcm_token){
            if($deliveryMan->enable_push_notification == 1){
                $payload = (object) [
                    "title"               => translate('New messsage'),
                    "message"             => translate('You have a new message form ').$this->deliveryman->first_name,
                    "deliveryman_id"      => $this->deliveryman->id,
                    "type"                => NotificationType::SELF_DELIVERYMAN_CHAT->value,
                ];

                $this->fireBaseNotification($deliveryMan->fcm_token,$payload);
            }
        }

        return api(
            [
                'message'  => translate('Message sent succesfully'),
            ])->success(__('response.success'));


    }
  
 




  


}
