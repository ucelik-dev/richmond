<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('discount_coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('discount_coupons')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->decimal('amount_discounted', 10, 2)->default(0);
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();

            $table->index(['coupon_id','student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_coupon_usages');
    }

};
