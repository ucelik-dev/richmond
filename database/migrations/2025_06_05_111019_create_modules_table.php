<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('level_id')->constrained('course_levels');
            $table->foreignId('awarding_body_id')->constrained('awarding_bodies');
            $table->text('description')->nullable();
            $table->text('overview')->nullable();
            $table->text('learning_outcomes')->nullable();
            $table->string('video_url')->nullable(); 
            $table->string('assignment_file')->nullable();
            $table->string('sample_assignment_file')->nullable();
            $table->integer('order')->default(0); 
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
