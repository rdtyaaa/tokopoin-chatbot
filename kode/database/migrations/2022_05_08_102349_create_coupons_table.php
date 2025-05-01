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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->tinyInteger('status')->default(1)->comment('Enable : 1, Disable : 2');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->decimal('value', 18,8)->default(0);
            $table->tinyInteger('type')->nullable()->comment('Fixed : 1, Percent : 2');
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
        Schema::dropIfExists('coupons');
    }
};
