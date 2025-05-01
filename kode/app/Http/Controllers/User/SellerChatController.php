<?php

namespace App\Http\Controllers\User;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\CustomerSellerConversation;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Rules\General\FileExtentionCheckRule;
use App\Traits\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerChatController extends Controller
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

        $productId = request()->query('product_id');
        if($productId){
            $product = Product::findOrFail($productId);

            $url = $product->product_type == Product::DIGITAL ? route('digital.product.details', [make_slug($product->name), $product->id])
            :route('product.details',[make_slug($product->name),$product->id]);
        }

        $sellerIds = CustomerSellerConversation::with(['customer','customer.country'])
                                    ->where('customer_id',$this->user->id)
                                    ->select('seller_id')
                                    ->distinct()
                                    ->pluck('seller_id')
                                    ->toArray();

        return view('user.chat.seller.list',[
            'title'       => translate("User to seller chat"),
            'sellers'     => Seller::with(['latestConversation'])->whereIn('id',   $sellerIds)->get(),
            'product_url' => @$url
        ]);


    }



    public function getChat ($seller_id):array {


        $seller = Seller::with(['sellerShop'])->find($seller_id);

        if(!$seller)  return    [
                                    'status'  => false,
                                    "message" => translate('Invalid delivery man')
                                ];


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
                                    ->where('seller_id',$seller_id)
                                    ->where('customer_id',$this->user->id)
                                    ->get();


        return [
            'status'  => true,
            "chat"    => view('user.chat.seller.message', compact('messages' , 'seller'))->render()
        ];

    }






    public function sendMessage(Request $request) : array {

        try {

            $request->validate([
                'seller_id' => 'required|exists:sellers,id',
                'message'   => 'required|max:191',
                'files.*'   => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')]
            ]);

            $seller = Seller::where('id',$request->input('seller_id'))->first();

            if(!$seller) return ['status'=> false , 'message' => translate('Invalid seller')];

            $message                     = new CustomerSellerConversation();
            $message->message            = $request->input('message');
            $message->customer_id        = $this->user->id;
            $message->seller_id          = $request->input('seller_id');
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



            return ['status'       => true , 'seller_id' =>  $seller->id];

        } catch (\Exception $ex) {

            return ['status'       => false , 'message' => $ex->getMessage()];

        }

    }


}