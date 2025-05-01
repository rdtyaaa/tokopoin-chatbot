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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedInteger('seller_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->decimal('amount', 18,8)->default(0);
            $table->decimal('post_balance', 18,8)->default(0);
            $table->string('transaction_type', 10)->nullable();
            $table->string('transaction_number', 70)->nullable();
            $table->string('details')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
