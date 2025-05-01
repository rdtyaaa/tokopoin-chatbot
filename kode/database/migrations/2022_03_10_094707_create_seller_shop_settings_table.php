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
        Schema::create('seller_shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedInteger('seller_id');
            $table->string('short_details')->nullable();
            $table->string('name')->nullable()->unique();
            $table->string('email', 120)->nullable()->unique();
            $table->string('phone', 120)->nullable()->unique();
            $table->string('address')->nullable();
            $table->string('shop_logo')->nullable();
            $table->string('seller_site_logo')->default('default.png');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
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
        Schema::dropIfExists('seller_shop_settings');
    }
};
