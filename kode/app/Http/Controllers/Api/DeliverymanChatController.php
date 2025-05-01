<?php

namespace App\Http\Controllers\Api;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deliveryman\DeliveryManCollection;
use App\Http\Resources\Deliveryman\DeliveryManResource;
use App\Http\Resources\DeliverymanConversationCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Services\Deliveryman\DeliverymanService;
use App\Models\CustomerDeliverymanConversation;
use App\Models\DeliveryMan;
use App\Models\User;
use App\Rules\General\FileExtentionCheckRule;
use App\Traits\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliverymanChatController extends Controller
{
    
    use Notify;
    protected ? User $user;
    public function __construct()
    {


        $this->middleware(function ($request, $next) {
            $this->user = auth()->guard('api')->user()?->load(['country','billingAddress']);
            return $next($request);
        });
    }



    public function list ():JsonResponse {


    
        $deliveryManIds = CustomerDeliverymanConversation::with(['customer','customer.country'])
                                    ->where('customer_id',$this->user->id)
                                    ->select('deliveryman_id') 
                                    ->distinct()
                                    ->pluck('deliveryman_id')
                                    ->toArray();



        $deliverymen = DeliveryMan::with(['latestConversation'])->whereIn('id',   $deliveryManIds)->get();

        return api([ 

            'delivery_men'                  => new DeliveryManCollection($deliverymen),
        ])->success(__('response.success'));

    }
  


    public function getChat ($deliveryman_id):JsonResponse {


        $deliveryman = DeliveryMan::with(['country'])
                                            ->where('id',$deliveryman_id)
                                            ->first();

        if(!$deliveryman)  return api(['errors'=> [translate("Invalid delivery man")]])->fails(__('response.fail'));
        



        CustomerDeliverymanConversation::with(['customer','customer.country','deliveryMan'])
                                    ->latest()
                                    ->where('deliveryman_id',$deliveryman_id)
                                    ->where('customer_id',$this->user->id)
                                    ->where("sender_role",'deliveryman')
                                    ->lazyById(100,'id')
                                    ->each
                                    ->update([
                                        'is_seen' => 1
                                    ]);
        
        $messages = CustomerDeliverymanConversation::with(['customer','customer.country','deliveryMan'])
                                    ->latest()
                                    ->where('deliveryman_id',$deliveryman_id)
                                    ->where('customer_id',$this->user->id)
                                    ->paginate(paginate_number());
              
                   

        return api([ 
            'user'           => new UserResource($this->user),
            'delivery_man'   => new DeliveryManResource($deliveryman),
            'messages'       => new DeliverymanConversationCollection($messages)
        ])->success(__('response.success'));

    }



    /**
     * Reply to a new ticket
     *
     * @return JsonResponse
     */
    public function sendMessage(Request $request) : JsonResponse {

        $request->validate([
            'deliveryman_id' => 'required|exists:delivery_men,id',
            'message' => 'required|max:191',
            'files.*'  => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')] 
        ]);



        $deliveryman = DeliveryMan::with(['country'])
                                    ->where('id',$request->input('deliveryman_id'))
                                    ->first();

        $message                     = new CustomerDeliverymanConversation();
        $message->message            = $request->input('message');
        $message->customer_id        = $this->user->id;
        $message->deliveryman_id     = $request->input('deliveryman_id');
        $message->sender_role        = 'customer';
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
        if($deliveryman && 
           $deliveryman->fcm_token &&  
           $deliveryman->enable_push_notification == 1){
            $payload = (object) [
                "title"               => translate('New messsage'),
                "message"             => translate('You have a new message form ').$this->user->name,
                "user_id"             => $this->user->id,
                "type"                => NotificationType::CUSTOMER_CHAT->value,
            ];
            $this->fireBaseNotification($deliveryman->fcm_token,$payload);
        }

        return api(
            [
                'message'  => translate('Message sent succesfully'),
            ])->success(__('response.success'));
    }
  
 
}
