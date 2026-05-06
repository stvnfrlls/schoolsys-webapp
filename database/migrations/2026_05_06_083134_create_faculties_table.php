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
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('employee_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            $table->string('contact_number')->nullable();

            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->enum('rank', [
                'instructor',
                'assistant_professor',
                'associate_professor',
                'professor',
            ])->nullable();
            $table->string('specialization')->nullable();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
            ])->default('full_time');
            $table->enum('status', ['active', 'inactive', 'retired'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
