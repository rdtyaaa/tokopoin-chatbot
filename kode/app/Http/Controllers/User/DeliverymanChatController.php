<?php

namespace App\Http\Controllers\User;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\CustomerDeliverymanConversation;
use App\Models\DeliveryMan;
use App\Models\User;
use App\Rules\General\FileExtentionCheckRule;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;


class DeliverymanChatController extends Controller
{

    use Notify;
    protected ? User $user;
    public function __construct()
    {


        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            return $next($request);
        });
    }



    public function list () {

        $deliveryManIds = CustomerDeliverymanConversation::with(['customer','customer.country'])
                                ->where('customer_id',$this->user->id)
                                ->select('deliveryman_id')
                                ->distinct()
                                ->pluck('deliveryman_id')
                                ->toArray();

        return view('user.chat.delivery_man.list',[
            'title'       => translate("User to deliveryman chat"),
            'deliverymen' => DeliveryMan::with(['latestConversation'])->whereIn('id',   $deliveryManIds)->get()
        ]);

    }



    public function getChat ($deliveryman_id) :array{


        $deliveryman = DeliveryMan::where('id',$deliveryman_id)
                                            ->first();

        if(!$deliveryman) return [
                                    'status'  => false,
                                    "message" => translate('Invalid delivery man')
                                ];


            CustomerDeliverymanConversation::latest()
                                            ->where('deliveryman_id',$deliveryman_id)
                                            ->where('customer_id',$this->user->id)
                                            ->where("sender_role",'deliveryman')
                                            ->lazyById(100,'id')
                                            ->each
                                            ->update([
                                                'is_seen' => 1
                                            ]);



                $messages = CustomerDeliverymanConversation::with(['customer', 'customer.country', 'deliveryMan'])
                                                ->where('deliveryman_id', $deliveryman_id)
                                                ->where('customer_id', $this->user->id)
                                                ->get();




                return [


                    'status'       => true,
                    "chat"         => view('user.chat.delivery_man.message', compact('messages' , 'deliveryman'))->render()
                ];


    }

    public function sendMessage(Request $request) :array{

        try {

            $request->validate([
                'deliveryman_id' => 'required|exists:delivery_men,id',
                'message'        => 'required|max:191',
                'files.*'        => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')]
            ]);

            $deliveryman = DeliveryMan::with(['country'])
                                    ->where('id',$request->input('deliveryman_id'))
                                    ->first();
            if(!$deliveryman) return ['status'=> false , 'message' => translate('Invalid deliveryman')];

            $message                     = new CustomerDeliverymanConversation();
            $message->message            = $request->input('message');
            $message->customer_id        = $this->user->id;
            $message->deliveryman_id     = $request->input('deliveryman_id');
            $message->sender_role        = 'customer';
            $message->is_seen            = 0;


            if($request->hasFile('files')) {
                $files   =  [];
                foreach ($request->file('files') as $file) {
                    $files []     = upload_new_file($file, file_path()['chat']['path']);
                }
                $message->files =    $files;
            }

            $message->save();


             #FIREBASE NOTIFICATIONS
            if($deliveryman &&  $deliveryman->fcm_token){
                $payload = (object) [
                    "title"               => translate('New messsage'),
                    "message"             => translate('You have a new message form ').$this->user->name,
                    "user_id"             => $this->user->id,
                    "type"                => NotificationType::CUSTOMER_CHAT->value,
                ];
                $this->fireBaseNotification($deliveryman->fcm_token,$payload);
            }


            return [ 'status'       => true ,'deliveryman_id' =>  $deliveryman->id ];

        } catch (\Exception $ex) {

            return ['status'=> false , 'message' => $ex->getMessage()];
        }

    }

}
