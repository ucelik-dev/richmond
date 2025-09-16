<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('document_categories')->onDelete('set null');
            $table->string('path'); // the file path (e.g. uploads/user-documents/file.pdf)
            $table->date('date')->nullable(); // optional date (e.g. agreement date, issue date, etc.)
            $table->timestamps();

            $table->unique(['user_id', 'category_id']); // Optional: prevents duplicates
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
