<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('recruitment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "pending", "called", "registered"
            $table->string('label')->nullable(); // Human-readable version (optional)
            $table->string('color')->nullable(); // e.g., "blue", "green" for UI use
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_statuses');
    }
    
};
