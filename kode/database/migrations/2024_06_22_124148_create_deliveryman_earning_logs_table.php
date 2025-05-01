<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliverymanEarningLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveryman_earning_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('assigned_id')->nullable();
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('deliveryman_id')->nullable();
            $table->decimal('amount', 18,8)->default(0);
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
        Schema::dropIfExists('deliveryman_earning_logs');
    }
}
