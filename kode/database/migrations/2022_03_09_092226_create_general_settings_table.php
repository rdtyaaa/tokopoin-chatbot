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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string('site_name')->nullable();
            $table->string('logo')->nullable();
            $table->longText('invoice_logo')->nullable();
            $table->string('admin_panel_logo')->nullable();
            $table->string('cod')->nullable();
            $table->text('copyright_text')->nullable();
            $table->string('seller_mode')->nullable();
            $table->string('favicon')->nullable();
            $table->string('currency_name', 10)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->string('primary_color')->nullable();
            $table->integer('pagination_number')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('font_color')->nullable();
            $table->unsignedBigInteger('sms_gateway_id')->nullable();
            $table->decimal('commission', 18,8)->nullable();
            $table->string('mail_from')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('email_gateway_id')->nullable();
            $table->text('email_template')->nullable();
            $table->text('sms_template')->nullable();
            $table->text('s_login_google_info')->nullable();
            $table->text('s_login_facebook_info')->nullable();
            $table->tinyInteger('email_notification',)->nullable()->comment('Enable : 1, Disable : 2');
            $table->tinyInteger('refund')->nullable()->comment('ON : 1, OFF : 2');
            $table->integer('refund_time_limit')->default(10)->nullable();
            $table->tinyInteger('sms_notification',)->nullable()->comment('Enable : 1, Disable : 2');
            $table->tinyInteger('seller_reg_allow',)->nullable()->comment('ON : 1, OFF : 2');
            $table->decimal('search_min',18,8)->nullable();
            $table->decimal('search_max',18,8)->nullable();
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
        Schema::dropIfExists('general_settings');
    }
};
