<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();

            // permission_id should point to a "resource" row in permissions (e.g. 'admin_payments')
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('permission_id')
                  ->constrained('permissions')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // per-action toggles
            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);

            $table->timestamps();

            // one row per (user, resource)
            $table->unique(['user_id', 'permission_id'], 'user_permissions_user_resource_unique');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }

};
