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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('product_type')->nullable()->comment('Digital Product : 101,  Physical Product : 102');
            $table->unsignedInteger('seller_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('sub_category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->decimal('price', 18,8)->default(0);
            $table->decimal('discount', 18,8)->nullable();
            $table->decimal('discount_percentage', 18,8)->nullable();
            $table->string('featured_image')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('shipping_country')->nullable();
            $table->text('attributes')->nullable();
            $table->text('attributes_value')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_image')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('minimum_purchase_qty')->nullable();
            $table->integer('maximum_purchase_qty')->nullable();
            $table->tinyInteger('top_status')->default(1)->comment('No : 1, Yes : 2');
            $table->tinyInteger('featured_status')->default(1)->comment('No : 1, Yes : 2');
            $table->tinyInteger('best_selling_item_status')->default(1)->comment('No : 1, Yes : 2');
            $table->tinyInteger('status')->default(0)->comment('New : 0, Published : 1, Inactive : 2');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
};
