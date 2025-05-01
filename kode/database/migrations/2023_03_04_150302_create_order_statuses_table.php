<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->tinyInteger('payment_status')->nullable()->comment("Unpaid : 1, Paid : 2");
            $table->string('payment_note')->nullable();
            $table->tinyInteger('delivery_status')->nullable()->comment("Placed : 1, Confirmed : 2, Processing : 3, Shipped : 4, Delivered : 5 Cancel : 6");
            $table->string('delivery_note')->nullable();
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
        Schema::dropIfExists('order_statuses');
    }
}
