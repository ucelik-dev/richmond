<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('group_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // instructor who shared
            $table->string('title');
            $table->text('content')->nullable(); // for text messages or notes
            $table->string('link')->nullable(); // for uploaded file path
            $table->string('file')->nullable(); // for uploaded file path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_shares');
    }
};
