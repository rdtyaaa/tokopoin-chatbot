<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Enums\Settings\NotificationType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\DB;

class CustomerChatController extends Controller
{


    use Notify;
    protected ? DeliveryMan $deliveryman;

    public function __construct(protected DeliverymanService $deliverymanService){
        $this->middleware(function ($request, $next) {
            $this->deliveryman = auth()->guard('delivery_man:api')->user()?->load(['ratings','orders','refferedBy']);


            if(site_settings('chat_with_customer') != StatusEnum::true->status()) return api(['errors' => [translate('Chat with customers currently off.')]])->fails(__('response.error'));

            return $next($request);
        });
    }

    




    public function list ():JsonResponse {


        $customerIds = CustomerDeliverymanConversation::with(['customer','customer.country'])
                                                ->latest()
                                                ->where('deliveryman_id',$this->deliveryman->id)
                                                ->select('customer_id') 
                                                ->distinct()
                                                ->pluck('customer_id')
                                                ->toArray();


        $users = User::with(['country','latestDeliveryManMessage'])
                                            ->whereIn('id',$customerIds)
                                            ->get();


        return api([ 
            'customer'                  => new UserCollection($users)
        ])->success(__('response.success'));

    }
  


    public function getChat ($customer_id):JsonResponse {


        $user = User::with(['country','billingAddress'])
                                            ->where('id',$customer_id)
                                            ->first();

        
                                        
        if(!$user)  return api(['errors'=> [translate("Invalid user")]])->fails(__('response.fail'));


        CustomerDeliverymanConversation::with(['customer','customer.country','deliveryMan'])
                                    ->latest()
                                    ->where('deliveryman_id',$this->deliveryman->id)
                                    ->where('customer_id',$customer_id)
                                    ->where("sender_role",'customer')
                                    ->lazyById(100,'id')
                                    ->each
                                    ->update([
                                        'is_seen' => 1
                                    ]);
        

        $messages = CustomerDeliverymanConversation::with(['customer','customer.country','deliveryMan'])
                                    ->latest()
                                    ->where('deliveryman_id',$this->deliveryman->id)
                                    ->where('customer_id',$customer_id) 
                                    ->paginate(paginate_number());
              
                   

        return api([ 

            'user'           => new UserResource($user),
            'delivery_man'   => new DeliveryManResource($this->deliveryman),
            'messages'                  => new DeliverymanConversationCollection($messages),
        ])->success(__('response.success'));

    }


    public function deleteConversation ($customer_id) :JsonResponse{

        CustomerDeliverymanConversation::latest()
                                    ->where('deliveryman_id',$this->deliveryman->id)
                                    ->where('customer_id',$customer_id)
                                    ->lazyById(100,'id')
                                    ->each
                                    ->delete();
        
        return api(
            [
                'message'  => translate('Conversation deleted'),
            ])->success(__('response.success'));
                            

    }



    /**
     * Reply to a new ticket
     *
     * @return JsonResponse
     */
    public function sendMessage(Request $request) : JsonResponse {

        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'message' => 'required|max:191',
            'files.*'  => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')] 
        ]);


        $user = User::where('id',$request->input('customer_id'))
                            ->first();

        $message                     = new CustomerDeliverymanConversation();
        $message->message            = $request->input('message');
        $message->customer_id        = $request->input('customer_id');
        $message->deliveryman_id     = $this->deliveryman->id;
        $message->sender_role        = 'deliveryman';
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
        if($user &&  $user->fcm_token){
            $payload = (object) [
                "title"               => translate('New messsage'),
                "message"             => translate('You have a new message form ').$this->deliveryman->first_name,
                "deliveryman_id"      => $this->deliveryman->id,
                "type"                => NotificationType::DELIVERYMAN_CHAT->value,
            ];
            $this->fireBaseNotification($user->fcm_token, $payload );
        }

        return api(
            [
                'message'  => translate('Message sent succesfully'),
            ])->success(__('response.success'));


    }
  
 




  


}
