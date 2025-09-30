<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->default('/uploads/profile-images/avatar.png');
            $table->string('name');     
            $table->string('code')->nullable()->unique(); 
            $table->string('url')->nullable();       
            $table->string('phone')->nullable();    
            $table->string('email')->nullable();
            $table->boolean('status')->default(true);
            $table->text('bank_account')->nullable();
            $table->text('invoice_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
