<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();        
            $table->date('rc_graduation_date')->nullable();
            $table->date('top_up_date')->nullable();
            $table->string('university')->nullable();
            $table->string('program')->nullable();
            $table->string('study_mode')->nullable();
            $table->date('program_entry_date')->nullable();
            $table->boolean('job_status')->default(false);
            $table->string('job_title')->nullable();
            $table->date('job_start_date')->nullable();
            $table->text('note')->nullable();
            $table->string('diploma_file')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduates');
    }

};
