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
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('name')->nullable();
            $table->decimal('amount', 18,8)->default(0);
            $table->integer('total_product')->nullable();
            $table->integer('duration')->nullable();
            $table->tinyInteger('status')->default(0)->comment('Active : 1, Inactive : 2');
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
        Schema::dropIfExists('pricing_plans');
    }
};
