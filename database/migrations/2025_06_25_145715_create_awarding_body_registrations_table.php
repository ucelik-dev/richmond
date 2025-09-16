<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('awarding_body_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // link to users
            $table->foreignId('awarding_body_id')->constrained()->onDelete('cascade'); // link to awarding_bodies
            $table->foreignId('awarding_body_registration_level_id')->constrained('course_levels')->nullOnDelete(); 
            $table->string('awarding_body_registration_number');
            $table->date('awarding_body_registration_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awarding_body_registrations');
    }

};
