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
        Schema::create('digital_product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('digital_product_attribute_id');
            $table->text('value')->nullable();
            $table->tinyInteger('status')->default(0)->comment('Active : 1, sold : 2');
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
        Schema::dropIfExists('digital_product_attribute_values');
    }
};
