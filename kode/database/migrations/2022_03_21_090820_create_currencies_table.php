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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('name', 50)->nullable();
            $table->string('symbol', 10)->nullable();
            $table->decimal('rate', 18,8)->default(0);
            $table->tinyInteger('status')->default(1)->comment('Active : 1, Inactive : 2, default : 3');
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
        Schema::dropIfExists('currencies');
    }
};
