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
        Schema::create('subject_per_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gradelevel_id')->constrained('grade_levels')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedSmallInteger('hours_per_week')->nullable();
            $table->enum('is_active', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['gradelevel_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_per_levels');
    }
};
