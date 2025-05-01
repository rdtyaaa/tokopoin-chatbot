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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('name')->nullable();
            $table->string('username',70)->nullable()->unique();
            $table->string('email',70)->nullable()->unique();
            $table->integer('rating')->nullable();
            $table->string('image',191)->nullable();
            $table->string('phone',70)->nullable()->unique();
            $table->text('address')->nullable();
            $table->decimal('balance', 18,8)->default(0);
            $table->string('password')->nullable();
            $table->tinyInteger('best_seller_status')->default(1)->comment('No : 1, Yes : 2');
            $table->tinyInteger('status')->default(0)->comment('Active : 1, Banned : 0');
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
        Schema::dropIfExists('sellers');
    }
};
