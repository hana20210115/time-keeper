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
        Schema::create('rest_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rest_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('attendance_correction_id')->constrained()->onDelete('cascade');
            $table->time('start');
            $table->time('end');
            $table->tinyInteger('status')->default(0); // 0 = 承認待ち, 1 = 承認済み,
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rest_corrections');
    }
};
