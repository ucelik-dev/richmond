<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->foreignId('agent_id')->constrained('users')->restrictOnDelete(); // coupon owner
            $table->enum('discount_type', ['percent','fixed']);
            $table->decimal('discount_value', 10, 2);          // percent: 1–100, fixed: currency
            $table->unsignedInteger('max_uses')->nullable();   // null = unlimited
            $table->decimal('min_amount', 10, 2)->nullable();  // optional threshold
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete(); 
            $table->timestamps();

            $table->index(['agent_id','active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
    
};
