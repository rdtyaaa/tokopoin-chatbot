<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatbotSetting;
use App\Models\SellerShopSetting;
use Illuminate\Support\Facades\Auth;

class ChatbotSettingController extends Controller
{
    public function edit()
    {
        $title = translate('Chatbot Setting');
        $seller = auth()->guard('seller')->user();
        $chatbotSetting = ChatbotSetting::firstOrCreate(['seller_id' => $seller->id]);

        $sellerShop = SellerShopSetting::where('seller_id', $seller->id)->first();

        return view('seller.shop.chatbot_setting', compact('chatbotSetting', 'title', 'sellerShop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'trigger_when_offline' => 'boolean',
            'trigger_when_no_reply' => 'boolean',
            'delay_minutes' => 'nullable|integer|min:1|max:120',
            'response_delay' => 'nullable|integer|min:1|max:60',
            'whatsapp_notify_new_message' => 'boolean',
            'whatsapp_notify_chatbot_reply' => 'boolean',
            'whatsapp_notify_no_reply' => 'boolean',
        ]);

        $seller = auth()->guard('seller')->user();
        $setting = ChatbotSetting::firstOrCreate(['seller_id' => $seller->id]);

        $setting->status = $request->status;
        $setting->trigger_when_offline = $request->has('trigger_when_offline');
        $setting->trigger_when_no_reply = $request->has('trigger_when_no_reply');
        $setting->delay_minutes = $request->delay_minutes;
        $setting->response_delay = $request->response_delay;
        $setting->whatsapp_notify_new_message = $request->has('whatsapp_notify_new_message');
        $setting->whatsapp_notify_chatbot_reply = $request->has('whatsapp_notify_chatbot_reply');
        $setting->whatsapp_notify_no_reply = $request->has('whatsapp_notify_no_reply');
        $setting->save();

        return redirect()->route('seller.chatbot.setting.edit')->with('success', translate('Chatbot settings updated successfully'));
    }
}
