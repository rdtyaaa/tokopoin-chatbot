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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('serial')->default(1);
            $table->json('name')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('banner')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_image')->nullable();
            $table->enum('feature',[0,1])->default(0)->comment('No : 0, Yes : 1')->nullable();
            $table->enum('status',[0,1])->default(1)->comment('Active : 1,Inactive : 0');
            $table->enum('top',[0,1])->default(0)->comment('No : 0, Yes : 1')->nullable();
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
        Schema::dropIfExists('categories');
    }
};
