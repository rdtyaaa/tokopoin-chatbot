<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_seller_conversations', function (Blueprint $table) {
            $table->enum('source', ['web', 'whatsapp'])->default('web')->after('is_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_seller_conversations', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
