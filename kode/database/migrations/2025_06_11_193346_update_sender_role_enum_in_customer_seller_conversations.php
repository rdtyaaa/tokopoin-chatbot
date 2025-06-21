<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSenderRoleEnumInCustomerSellerConversations extends Migration
{
    public function up()
    {
        Schema::table('customer_seller_conversations', function (Blueprint $table) {
            $table->dropColumn('sender_role');
        });

        Schema::table('customer_seller_conversations', function (Blueprint $table) {
            $table->enum('sender_role', ['customer', 'seller', 'chatbot'])->after('seller_id');
        });
    }

    public function down()
    {
        Schema::table('customer_seller_conversations', function (Blueprint $table) {
            $table->dropColumn('sender_role');
        });

        Schema::table('customer_seller_conversations', function (Blueprint $table) {
            $table->enum('sender_role', ['customer', 'seller'])->after('seller_id');
        });
    }
}

