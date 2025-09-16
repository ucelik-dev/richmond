<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('recruitment_calls', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('recruitment_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('called_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            $table->enum('communication_method', [
                'call',
                'message'
            ])->nullable();
            
            $table->foreignId('status_id')
                ->nullable()
                ->constrained('recruitment_statuses')
                ->nullOnDelete();

            $table->timestamp('called_at')->nullable(); 
            $table->text('note')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_calls');
    }

};
