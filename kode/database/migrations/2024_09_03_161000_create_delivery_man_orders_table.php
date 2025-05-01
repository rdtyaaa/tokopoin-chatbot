<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryManOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_man_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('assign_by')->nullable();
            $table->unsignedInteger('deliveryman_id')->nullable();
            $table->longText('pickup_location')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->longText('note')->nullable();
            $table->longText('feedback')->nullable();
            $table->longText('rejected_reason')->nullable();
            $table->longText('time_line')->nullable();
            $table->decimal('amount', 18,8)->default(0);
            $table->string('details')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
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
        Schema::dropIfExists('delivery_man_orders');
    }
}
