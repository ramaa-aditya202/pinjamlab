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
        Schema::create('lab_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
            $table->integer('hour'); // jam ke 1-9
            $table->string('subject'); // mata pelajaran
            $table->string('class'); // kelas
            $table->string('teacher'); // guru pengampu
            $table->boolean('is_fixed')->default(true); // jadwal pakem/tetap
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_schedules');
    }
};
