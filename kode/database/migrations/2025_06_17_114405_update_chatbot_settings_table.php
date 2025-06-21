<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateChatbotSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('chatbot_settings', function (Blueprint $table) {
            // Remove old mode column
            $table->dropColumn('mode');

            // Add new trigger columns
            $table->boolean('trigger_when_offline')->default(true)->after('status');
            $table->boolean('trigger_when_no_reply')->default(false)->after('trigger_when_offline');

            // Add WhatsApp notification columns
            $table->boolean('whatsapp_notify_new_message')->default(false)->after('response_delay');
            $table->boolean('whatsapp_notify_chatbot_reply')->default(false)->after('whatsapp_notify_new_message');
            $table->boolean('whatsapp_notify_no_reply')->default(false)->after('whatsapp_notify_chatbot_reply');
        });
    }

    public function down()
    {
        Schema::table('chatbot_settings', function (Blueprint $table) {
            // Restore old mode column
            $table->enum('mode', ['offline', 'delayed'])->default('offline')->after('status');

            // Remove new columns
            $table->dropColumn([
                'trigger_when_offline',
                'trigger_when_no_reply',
                'whatsapp_notify_new_message',
                'whatsapp_notify_chatbot_reply',
                'whatsapp_notify_no_reply'
            ]);
        });
    }
}
