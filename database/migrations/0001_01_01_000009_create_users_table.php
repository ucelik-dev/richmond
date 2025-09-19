<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('college_id')->nullable()->constrained('colleges')->nullOnDelete();

            $table->string('image')->default('/uploads/profile-images/avatar.png');

            $table->string('name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();

            $table->string('phone');
            $table->string('email');
            $table->string('payment_email')->nullable();
            $table->string('dob')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('city')->nullable();
            $table->string('post_code')->nullable();
            $table->string('address')->nullable();
            $table->string('company')->nullable();
            $table->text('bio')->nullable();

            $table->foreignId('sales_person_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('account_status')->default(1);

            $table->foreignId('user_status_id')->nullable()->constrained('user_statuses')->nullOnDelete();

            $table->enum('approve_status', ['pending','approved','declined','suspended','ongoing','withdrawn','graduated'])->default('pending');

            $table->integer('login_count')->default(0);
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
