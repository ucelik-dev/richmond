<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade'); // Links to the total plan
            $table->date('due_date');
            $table->double('amount');

            $table->enum('provider', ['manual', 'paypal', 'stripe', 'wise'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('provider_response')->nullable();

            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
    
};
