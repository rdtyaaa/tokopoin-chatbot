<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Seller;
use App\Traits\Notify;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Enums\Settings\NotificationType;
use App\Models\CustomerSellerConversation;

class SellerChatController extends Controller
{
    use Notify;
    protected ?User $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            return $next($request);
        });
    }

    public function list()
    {
        $productId = request()->query('product_id');
        $sellerId = request()->query('seller_id');

        $sellerIds = CustomerSellerConversation::with(['customer', 'customer.country'])
            ->where('customer_id', $this->user->id)
            ->select('seller_id')
            ->distinct()
            ->pluck('seller_id')
            ->toArray();

        // Jika ada seller_id dari query dan belum ada di conversation, tambahkan
        if ($sellerId && !in_array($sellerId, $sellerIds)) {
            $sellerIds[] = $sellerId;
        }

        $sellers = Seller::with(['latestConversation'])
            ->whereIn('id', $sellerIds)
            ->get();

        // Jika seller dari query belum pernah chat, tambahkan ke collection
        if ($sellerId && !$sellers->contains('id', $sellerId)) {
            $newSeller = Seller::find($sellerId);
            if ($newSeller) {
                $newSeller->latestConversation = null;
                $sellers->prepend($newSeller);
            }
        }

        return view('user.chat.seller.list', [
            'title' => translate('User to seller chat'),
            'sellers' => $sellers,
        ]);
    }

    public function getChat($seller_id): array
    {
        $seller = Seller::with(['sellerShop'])->find($seller_id);

        if (!$seller) {
            return [
                'status' => false,
                'message' => translate('Invalid seller'),
            ];
        }

        CustomerSellerConversation::with(['customer', 'customer.country', 'seller', 'seller.sellerShop'])
            ->latest()
            ->where('seller_id', $seller_id)
            ->where('customer_id', $this->user->id)
            ->where('sender_role', 'seller')
            ->lazyById(6, 'id')
            ->each->update([
                'is_seen' => 1,
            ]);

        $messages = CustomerSellerConversation::with(['customer', 'customer.country', 'seller', 'seller.sellerShop'])
            ->where('seller_id', $seller_id)
            ->where('customer_id', $this->user->id)
            ->get();

        return [
            'status' => true,
            'chat' => view('user.chat.seller.message', compact('messages', 'seller'))->render(),
        ];
    }

    public function sendMessage(Request $request): array
    {
        try {
            $request->validate([
                'seller_id' => 'required|exists:sellers,id',
                'message' => 'required|max:191',
                'product_id' => 'nullable|exists:products,id',
            ]);

            $seller = Seller::where('id', $request->input('seller_id'))->first();

            if (!$seller) {
                return ['status' => false, 'message' => translate('Invalid seller')];
            }

            $message = new CustomerSellerConversation();
            $message->message = $request->input('message');
            $message->customer_id = $this->user->id;
            $message->seller_id = $request->input('seller_id');
            $message->sender_role = 'customer';
            $message->is_seen = 0;

            // Handle product attachment
            if ($request->input('product_id')) {
                $product = Product::find($request->input('product_id'));
                if ($product) {
                    $productUrl = $product->product_type == Product::DIGITAL
                        ? route('digital.product.details', [make_slug($product->name), $product->id])
                        : route('product.details', [make_slug($product->name), $product->id]);

                    // Add product info as attachment
                    $productAttachment = [
                        'type' => 'product',
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_url' => $productUrl,
                        'product_image' => $product->image
                    ];

                    $message->files = [json_encode($productAttachment)];
                }
            }

            $message->save();

            event(new NewMessageSent($message, $this->user->id, $request->input('seller_id')));
            Log::info('Sending NewMessageSent event', [
                'seller_id' => $request->input('seller_id'),
                'customer_id' => $this->user->id,
            ]);

            return ['status' => true, 'seller_id' => $seller->id];
        } catch (\Exception $ex) {
            return ['status' => false, 'message' => $ex->getMessage()];
        }
    }

    public function sidebar()
    {
        $sellerIds = CustomerSellerConversation::where('customer_id', auth()->id())
            ->select('seller_id')
            ->distinct()
            ->pluck('seller_id')
            ->toArray();

        $sellers = Seller::with(['latestConversation'])
            ->whereIn('id', $sellerIds)
            ->get();

        return view('user.chat.seller.sidebar', compact('sellers'));
    }
}
