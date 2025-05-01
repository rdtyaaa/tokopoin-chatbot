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
        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('currency_id')->nullable();
            $table->string('name', 70)->nullable();
            $table->string('image', 120)->nullable();
            $table->decimal('min_limit', 18,8)->default(0);
            $table->decimal('max_limit', 18,8)->default(0);
            $table->string('duration',40)->nullable();
            $table->decimal('fixed_charge', 18,8)->default(0);
            $table->decimal('percent_charge', 18,8)->default(0);
            $table->decimal('rate', 18,8)->default(0);
            $table->text('user_information')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0)->comment('Active : 1, Inactive : 0');
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
        Schema::dropIfExists('withdraw_methods');
    }
};
