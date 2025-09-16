<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('user_social_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('social_platform_id')->constrained()->onDelete('cascade');
            $table->string('link');
            $table->timestamps();

            $table->unique(['user_id', 'social_platform_id']); // Optional: prevents duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_social_platforms');
    }
};
