<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatbotSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->enum('mode', ['offline', 'delayed'])->default('offline');
            $table->integer('delay_minutes')->nullable(); // for delayed mode
            $table->integer('response_delay')->nullable(); // in seconds
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatbot_settings');
    }
}
