<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            $table->double('amount');           // Original price before discount
            $table->double('discount')->default(0); // Discount applied
            $table->double('total');     // Final amount after discount

            $table->string('currency')->default('GBP');
            $table->foreignId('status_id')->nullable()->constrained('payment_statuses')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
