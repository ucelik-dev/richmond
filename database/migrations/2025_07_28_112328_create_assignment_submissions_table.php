<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('assessor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('evaluated_at')->nullable();
            $table->enum('grade', ['pending', 'passed', 'merit', 'distinction', 'failed'])->default('pending');

            $table->string('feedback_file')->nullable(); // File uploaded by assessor
            $table->string('verification_file')->nullable(); // Internal verifier's file
            $table->string('plagiarism_report')->nullable(); // Internal verifier's file

            $table->text('feedback')->nullable();

            $table->boolean('extra_attempt')->default(false); // ✅ true if paid extra

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }

};
