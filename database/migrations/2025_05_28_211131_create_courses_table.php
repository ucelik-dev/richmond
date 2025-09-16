<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('extended_title');
            $table->foreignId('level_id')->constrained('course_levels');
            $table->foreignId('category_id')->constrained('course_categories');
            $table->foreignId('awarding_body_id')->constrained('awarding_bodies');
            $table->string('code');
            $table->string('credits');
            $table->string('thumbnail')->nullable();
            $table->string('logo')->nullable();
            
            $table->string('handbook_file')->nullable();
            $table->string('mapping_document')->nullable();
            $table->string('assignment_specification')->nullable();
            $table->string('curriculum')->nullable();

            $table->enum('demo_video_storage', ['upload','youtube','vimeo','external_link'])->nullable();
            $table->text('demo_video_source')->nullable();
            $table->text('description')->nullable();
            $table->text('overview')->nullable();
            $table->text('overview_details')->nullable();
            $table->text('learning_outcomes')->nullable();
            $table->double('price')->nullable();
            $table->double('discount')->nullable();
            $table->boolean('completion_test')->default(0)->nullable();
            $table->boolean('completion_certificate')->default(0)->nullable();
            $table->enum('status', ['active','inactive','draft'])->default('draft');
            $table->boolean('show_in_select')->default(1)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
