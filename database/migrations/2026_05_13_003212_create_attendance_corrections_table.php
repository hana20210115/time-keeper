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
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->time('rest1_start')->nullable();
            $table->time('rest1_end')->nullable();
            $table->time('rest2_start')->nullable();
            $table->time('rest2_end')->nullable();
            $table->date('date');
            $table->time('modified_start');
            $table->time('modified_end');
            $table->text('reason');
            $table->tinyInteger('status')->default(0); // 0 = 未承認, 1 = 承認済み, 2 = 却下

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
