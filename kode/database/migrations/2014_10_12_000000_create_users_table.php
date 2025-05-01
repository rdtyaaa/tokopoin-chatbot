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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('name')->nullable();
            $table->string('username',70)->unique()->nullable();
            $table->string('email',70)->unique()->nullable();
            $table->string('phone',40)->unique()->nullable();
            $table->decimal('balance', 18,8)->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->text('address')->nullable();
            $table->string('google_id')->nullable();
            $table->string('password');
            $table->string('image', 120)->nullable();
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
        Schema::dropIfExists('users');
    }
};
