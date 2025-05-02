<?php

namespace App\Http\Controllers\Seller;

use App\Models\User;
use App\Models\Seller;
use App\Traits\Notify;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Enums\Settings\NotificationType;
use App\Models\CustomerSellerConversation;
use App\Http\Resources\Seller\SellerResource;
use App\Rules\General\FileExtentionCheckRule;
use App\Http\Resources\Seller\SellerCollection;
use App\Http\Resources\SellerConversationCollection;
use App\Http\Resources\Deliveryman\DeliveryManCollection;
use App\Http\Resources\DeliverymanConversationCollection;

class CustomerChatController extends Controller
{
    use Notify;
    protected ?Seller $seller;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->seller = auth()
                ->guard('seller')
                ->user()
                ?->load(['sellerShop']);
            return $next($request);
        });
    }

    public function list()
    {
        $customerIds = CustomerSellerConversation::with(['customer', 'customer.country'])
            ->where('seller_id', $this->seller->id)
            ->select('customer_id')
            ->distinct()
            ->pluck('customer_id')
            ->toArray();

        return view('seller.chat.list', [
            'title' => translate('Customer to seller chat'),
            'customers' => User::with(['country', 'latestSellerMessage'])
                ->whereIn('id', $customerIds)
                ->get(),
        ]);
    }

    public function getChat($customer_id): array
    {
        $user = User::with(['country', 'billingAddress'])
            ->where('id', $customer_id)
            ->first();

        if (!$user) {
            return ['status' => false, 'message' => translate('Invalid user')];
        }

        CustomerSellerConversation::with(['customer', 'customer.country', 'seller', 'seller.sellerShop'])
            ->latest()
            ->where('customer_id', $customer_id)
            ->where('seller_id', $this->seller->id)
            ->where('sender_role', 'customer')
            ->lazyById(100, 'id')
            ->each->update([
                'is_seen' => 1,
            ]);

        $messages = CustomerSellerConversation::with(['customer', 'customer.country', 'seller', 'seller.sellerShop'])
            ->where('seller_id', $this->seller->id)
            ->where('customer_id', $customer_id)
            ->get();

        return [
            'status' => true,
            'chat' => view('seller.chat.message', compact('messages', 'user'))->render(),
        ];
    }

    public function sendMessage(Request $request): array
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:users,id',
                'message' => 'required|max:191',
                'files.*' => ['nullable', new FileExtentionCheckRule(['pdf', 'doc', 'exel', 'jpg', 'jpeg', 'png', 'jfif', 'webp'], 'file')],
            ]);

            $user = user::where('id', $request->input('customer_id'))->first();

            $message = new CustomerSellerConversation();
            $message->message = $request->input('message');
            $message->customer_id = $request->input('customer_id');
            $message->seller_id = $this->seller->id;
            $message->sender_role = 'seller';
            $message->is_seen = 0;

            if ($request->hasFile('files')) {
                $files = [];
                foreach ($request->file('files') as $file) {
                    try {
                        $files[] = upload_new_file($file, file_path()['chat']['path']);
                    } catch (\Exception $exp) {
                    }
                }
                $message->files = $files;
            }

            $message->save();

            event(new NewMessageSent($message, $request->input('customer_id'), $this->seller->id));
            Log::info('Sending NewMessageSent event', [
                'customer_id' => $request->input('seller_id'),
                'seller_id' => $this->seller->id,
            ]);
            // Trigger event untuk Redis
            // event(new NewMessageSent($message, $request->input('customer_id')));
            // Log::info('Event dispatched to Redis', ['message' => $request->input('message'), 'receiver_id' => $request->input('customer_id')]);


            #FIREBASE NOTIFICATIONS
            if ($user && $user->fcm_token) {
                $payload = (object) [
                    'title' => translate('New messsage'),
                    'message' => translate('You have a new message form ') . @$this->seller->name,
                    'seller_id' => $this->seller->id,
                    'type' => NotificationType::SELLER_CHAT->value,
                ];
                $this->fireBaseNotification($user->fcm_token, $payload);
            }

            return ['status' => true, 'customer_id' => $user->id];
        } catch (\Exception $ex) {
            return ['status' => false, 'message' => $ex->getMessage()];
        }
    }
}
