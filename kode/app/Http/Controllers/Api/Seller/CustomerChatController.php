<?php

namespace App\Http\Controllers\Api\Seller;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deliveryman\DeliveryManCollection;
use App\Http\Resources\DeliverymanConversationCollection;
use App\Http\Resources\Seller\SellerCollection;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Resources\SellerConversationCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\CustomerSellerConversation;
use App\Models\DeliveryMan;
use App\Models\Seller;
use App\Models\User;
use App\Rules\General\FileExtentionCheckRule;
use App\Traits\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerChatController extends Controller
{

    use Notify;
    protected ? Seller $seller;

    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);
            return $next($request);
        });
    }



    public function list ():JsonResponse {

           $customerIds = CustomerSellerConversation::with(['customer','customer.country'])
                            ->where('seller_id',$this->seller->id)
                            ->select('customer_id') 
                            ->distinct()
                            ->pluck('customer_id')
                            ->toArray();

            $users = User::with(['country','latestSellerMessage'])->whereIn('id',   $customerIds)->get();

            return api([ 
                'customers'                  => new UserCollection($users),
            ])->success(__('response.success'));

    }
  


    public function getChat($customer_id):JsonResponse{

        $user = User::with(['country','billingAddress'])
                            ->where('id',$customer_id)
                            ->first();

        if(!$user)  return api(['errors'=> [translate("Invalid user")]])->fails(__('response.fail'));



        CustomerSellerConversation::with(['customer','customer.country','seller','seller.sellerShop'])
                                    ->latest()
                                    ->where('customer_id',$customer_id)
                                    ->where('seller_id',$this->seller->id)
                                    ->where("sender_role",'customer')
                                    ->lazyById(100,'id')
                                    ->each
                                    ->update([
                                        'is_seen' => 1
                                    ]);
        
        $messages = CustomerSellerConversation::with(['customer','customer.country','seller','seller.sellerShop'])
                                    ->latest()
                                    ->where('seller_id',$this->seller->id)
                                    ->where('customer_id',$customer_id)
                                    ->paginate(paginate_number());
              
                   

        return api([ 
            'user'           => new UserResource($user),
            'seller'   => new SellerResource($this->seller),
            'messages'                  => new SellerConversationCollection($messages),
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

        $user = User::with(['country','billingAddress'])
                            ->where('id',$request->input('customer_id'))
                            ->first();
        $message                     = new CustomerSellerConversation();
        $message->message            = $request->input('message');
        $message->customer_id        = $request->input('customer_id');
        $message->seller_id          = $this->seller->id;
        $message->sender_role        = 'seller';
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
                "message"             => translate('You have a new message form ').@$this->seller->name,
                "seller_id"           => $this->seller->id,
                "type"                => NotificationType::SELLER_CHAT->value,
            ];
            $this->fireBaseNotification($user->fcm_token, $payload );
        }
        return api(
            [
                'message'  => translate('Message sent succesfully'),
            ])->success(__('response.success'));
    }
  
 
}
