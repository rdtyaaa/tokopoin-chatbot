<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('phone_code');
            $table->string('first_name',191);
            $table->string('last_name',191);
            $table->string('username',70)->unique()->nullable();
            $table->string('email',70)->unique()->nullable();
            $table->string('phone',40)->unique()->nullable();
            $table->decimal('balance', 25,8)->default(0);
            $table->decimal('order_balance', 25,8)->default(0);
            $table->string('password');
            $table->string('image')->nullable();
            $table->text('kyc_data')->nullable();
            $table->tinyInteger('is_kyc_verified')->nullable();
            $table->tinyInteger('is_online')->nullable();
            $table->tinyInteger('enable_push_notification')->nullable();
            $table->timestamp('last_login_time')->nullable();
            $table->text('address')->nullable();
            $table->enum('status',[0,1])->default(1)->comment('Active : 1,Inactive : 0');
            $table->rememberToken();
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
        Schema::dropIfExists('delivery_men');
    }
}
