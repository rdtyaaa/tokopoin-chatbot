<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->json('payment_method')->nullable();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('banner_image')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('discount_type',[0,1])->default(1)->comment('Percent  : 1,Flat : 0');
            $table->string('discount')->nullable();
            $table->enum('show_home_page',[0,1])->default(0)->comment('Yes : 1,No : 0');
            $table->enum('status',[0,1])->default(1)->comment('Active : 1,Inactive : 0');
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
        Schema::dropIfExists('campaigns');
    }
}
