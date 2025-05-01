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
   
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('uid',100)->index()->nullable();
                $table->integer('serial')->nullable();
                $table->json('name')->nullable();
                $table->string('logo',120)->nullable();
                $table->tinyInteger('status')->default(1)->comment('Active : 1, Inactive : 2')->nullable();
                $table->tinyInteger('top')->default(1)->comment('No : 1, Yes : 2')->nullable();
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
        Schema::dropIfExists('brands');
    }
};
