<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('seller_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('ticket_number', 50)->nullable();
            $table->string('subject')->nullable();
            $table->tinyInteger('status')->default(1)->comment('Running : 1, Answered : 2, Replied : 3, closed : 4');
            $table->tinyInteger('priority')->default(1)->comment('Low : 1, medium : 2 high: 3');
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
        Schema::dropIfExists('support_tickets');
    }
};
