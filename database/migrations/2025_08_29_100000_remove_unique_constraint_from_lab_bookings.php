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
        Schema::table('lab_bookings', function (Blueprint $table) {
            // Hapus unique constraint yang lama
            $table->dropUnique(['day', 'hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_bookings', function (Blueprint $table) {
            // Kembalikan unique constraint jika rollback
            $table->unique(['day', 'hour']);
        });
    }
};
