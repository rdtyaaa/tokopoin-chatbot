<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsLattersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_latters', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('heading')->nullable();
            $table->string('banner_image')->nullable();
            $table->longText('description')->nullable();
            $table->string('time_unit')->nullable();
            $table->string('time_duration')->nullable();
            $table->integer('discount_percentage')->nullable();
            $table->enum('discount',[0,1])->default(0)->comment('Active : 1,Inactive : 0');
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
        Schema::dropIfExists('news_latters');
    }
}
