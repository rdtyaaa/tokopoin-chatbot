<?php

namespace App\Http\Controllers\Api;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deliveryman\DeliveryManCollection;
use App\Http\Resources\DeliverymanConversationCollection;
use App\Http\Resources\Seller\SellerCollection;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Resources\SellerConversationCollection;
use App\Http\Resources\UserResource;
use App\Models\CustomerSellerConversation;
use App\Models\DeliveryMan;
use App\Models\Seller;
use App\Models\User;
use App\Rules\General\FileExtentionCheckRule;
use App\Traits\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class SellerChatController extends Controller
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


    
        $sellerIds = CustomerSellerConversation::with(['customer','customer.country'])
                                    ->where('customer_id',$this->user->id)
                                    ->select('seller_id') 
                                    ->distinct()
                                    ->pluck('seller_id')
                                    ->toArray();

        $sellers = Seller::with(['latestConversation'])->whereIn('id',   $sellerIds)->get();

        return api([ 

            'sellers'                  => new SellerCollection($sellers),
        ])->success(__('response.success'));

    }
  


    public function getChat ($seller_id):JsonResponse {


        $seller = Seller::with(['sellerShop'])->find($seller_id);
        
        if(!$seller)  return api(['errors'=> [translate("Invalid Seller")]])->fails(__('response.fail'));
        
        
        CustomerSellerConversation::with(['customer','customer.country','seller','seller.sellerShop'])
                                    ->latest()
                                    ->where('seller_id',$seller_id)
                                    ->where('customer_id',$this->user->id)
                                    ->where("sender_role",'seller')
                                    ->lazyById(100,'id')
                                    ->each
                                    ->update([
                                        'is_seen' => 1
                                    ]);
        
        $messages = CustomerSellerConversation::with(['customer','customer.country','seller','seller.sellerShop'])
                                    ->latest()
                                    ->where('seller_id',$seller_id)
                                    ->where('customer_id',$this->user->id)
                                    ->paginate(paginate_number());
              
                   

        return api([ 
            'user'           => new UserResource($this->user),
            'seller'         => new SellerResource($seller),
            'messages'       => new SellerConversationCollection($messages),
        ])->success(__('response.success'));

    }





    /**
     * Reply to a new ticket
     *
     * @return JsonResponse
     */
    public function sendMessage(Request $request) : JsonResponse {

        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'message' => 'required|max:191',
            'files.*'  => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')] 
        ]);

        $seller = Seller::with(['sellerShop'])->find($request->input('seller_id'));

        $message                     = new CustomerSellerConversation();
        $message->message            = $request->input('message');
        $message->customer_id        = $this->user->id;
        $message->seller_id     = $request->input('seller_id');
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
        if($seller &&  $seller->fcm_token){
            $payload = (object) [
                "title"               => translate('New messsage'),
                "message"             => translate('You have a new message form ').$this->user->name,
                "user_id"             => $this->user->id,
                "type"                => NotificationType::CUSTOMER_CHAT->value,
            ];
            $this->fireBaseNotification($seller->fcm_token, $payload );
        }


        return api(
            [
                'message'  => translate('Message sent succesfully'),
            ])->success(__('response.success'));
    }
  
 
}
