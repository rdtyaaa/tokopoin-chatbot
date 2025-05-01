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
        Schema::create('shipping_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('country', 120)->nullable();
            $table->string('name', 120)->nullable();
            $table->unsignedBigInteger('method_id')->nullable();
            $table->string('duration',120)->nullable();
            $table->decimal('price', 18,8)->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('shipping_deliveries');
    }
};
