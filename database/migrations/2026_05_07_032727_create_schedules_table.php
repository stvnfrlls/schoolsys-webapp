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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_year_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('section_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('subject_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('faculty_id')
                ->constrained('faculties')
                ->restrictOnDelete();

            // 1=Monday, 2=Tuesday, ..., 5=Friday (or up to 7 if needed)
            $table->tinyInteger('day_of_week')->unsigned()->comment('1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri');

            $table->time('time_start');
            $table->time('time_end');

            $table->string('room')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Conflict detection indexes
            // A teacher can't be in two places at the same time
            $table->index(['faculty_id', 'school_year_id', 'day_of_week'], 'idx_teacher_schedule');

            // A section can't have two subjects at the same time
            $table->index(['section_id', 'school_year_id', 'day_of_week'], 'idx_section_schedule');

            // Optional: room double-booking check
            $table->index(['room', 'school_year_id', 'day_of_week'], 'idx_room_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
