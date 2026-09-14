<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenant_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 30)->default('contributor'); // owner, steward, contributor, observer
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_memberships');
        Schema::dropIfExists('tenants');
    }
};
