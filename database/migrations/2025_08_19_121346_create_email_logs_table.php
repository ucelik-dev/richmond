<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mailable')->nullable();      // e.g. App\Mail\UserCreatedMail
            $table->string('subject')->nullable();
            $table->json('to')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('message_id')->nullable();    // SMTP Message-ID
            $table->longText('html')->nullable();
            $table->longText('text')->nullable();
            $table->json('headers')->nullable();
            $table->json('meta')->nullable();            // custom: installment_id, etc.
            $table->string('status')->default('sent');   // sending|sent|failed
            $table->string('log_key')->nullable()->unique(); // add once; keep unique
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }

};
