<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastSeenToUsersAndSellersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('last_seen')->nullable()->after('email_verified_at');
        });

        // Tambahkan ke tabel sellers
        Schema::table('sellers', function (Blueprint $table) {
            $table->dateTime('last_seen')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus dari tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_seen');
        });

        // Hapus dari tabel sellers
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn('last_seen');
        });
    }
}
