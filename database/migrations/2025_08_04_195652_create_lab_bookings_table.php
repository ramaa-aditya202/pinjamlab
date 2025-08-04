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
        Schema::create('lab_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('day', ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
            $table->integer('hour'); // jam ke 1-9
            $table->string('teacher_name'); // nama guru yang mengajukan
            $table->string('class'); // kelas
            $table->string('subject'); // mata pelajaran
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable(); // catatan dari admin
            $table->timestamps();
            
            // Pastikan tidak ada booking duplikat untuk hari dan jam yang sama
            $table->unique(['day', 'hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_bookings');
    }
};
