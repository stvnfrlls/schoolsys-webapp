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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('schedule_id')->constrained('schedules')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'late', 'absent', 'excused'])->default('absent');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // One row per student per schedule per day
            $table->unique(['schedule_id', 'student_id', 'date'], 'uniq_attendance');
            // Fast lookup: all records for a schedule on a date (take attendance)
            $table->index(['schedule_id', 'date'], 'idx_schedule_date');
            // Fast lookup: all records for a student (history / summary)
            $table->index(['student_id', 'date'], 'idx_student_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
