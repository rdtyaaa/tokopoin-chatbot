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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedInteger('shipping_deliverie_id')->nullable();
            $table->text('payment_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('order_id')->nullable();
            $table->tinyInteger('qty')->nullable();
            $table->decimal('shipping_charge', 18,8)->nullable();
            $table->decimal('payment_info', 18,8)->nullable();
            $table->decimal('discount', 18,8)->nullable();
            $table->decimal('amount', 18,8)->default(0)->nullable();
            $table->text('billing_information')->nullable();
            $table->tinyInteger('payment_status')->default(1)->comment('Unpaid : 1, Paid : 2');
            $table->tinyInteger('order_type')->default(1)->comment('Digital : 101, Physical : 102');
            $table->tinyInteger('payment_type')->default(1)->comment('Cash On Delivery : 1, Payment method : 2');
            $table->tinyInteger('status')->default(1)->comment('Placed : 1, Confirmed : 2, Processing : 3, Shipped : 4, Delivered : 5 Cancel : 6');
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
        Schema::dropIfExists('orders');
    }
};
