<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            
            $table->string('title');
            $table->text('content')->nullable(); // this could be HTML, video links, etc.
            $table->string('video_url')->nullable(); // optional video

            $table->integer('order')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
