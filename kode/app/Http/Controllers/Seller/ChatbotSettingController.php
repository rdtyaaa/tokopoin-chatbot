<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatbotSetting;
use Illuminate\Support\Facades\Auth;

class ChatbotSettingController extends Controller
{
    public function edit()
    {
        $title = translate('Chatbot Setting');
        $sellerId = Auth::id();
        $chatbotSetting = ChatbotSetting::firstOrCreate(['seller_id' => $sellerId]);
        return view('seller.shop.chatbot_setting', compact('chatbotSetting', 'title'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'mode' => 'required|in:offline,delayed',
            'delay_minutes' => 'nullable|integer|min:1|max:120',
            'response_delay' => 'nullable|integer|min:1|max:60',
        ]);

        $setting = ChatbotSetting::firstOrCreate(['seller_id' => Auth::id()]);
        $setting->status = $request->status;
        $setting->mode = $request->mode;
        $setting->delay_minutes = $request->mode === 'delayed' ? $request->delay_minutes : null;
        $setting->response_delay = $request->response_delay;
        $setting->save();

        return redirect()->route('seller.shop.chatbot_setting')->with('success', 'Chatbot settings updated successfully.');
    }
}
