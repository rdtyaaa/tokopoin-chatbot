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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('campaign_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
            $table->json('attribute')->nullable();
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
        Schema::dropIfExists('carts');
    }
};
