<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Reviews (ARF)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('period', 30); // Q1-2026, Annual-2025, etc.
            $table->date('review_date');
            $table->string('status', 30)->default('open'); // open, closed
            $table->text('summary')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'period']);
        });

        // 2. Review Items (Findings per Module)
        Schema::create('review_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->string('topic');
            $table->string('status', 30)->default('identified');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Improvements (Actionable items resulting from Review)
        Schema::create('improvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('review_item_id')->nullable()->constrained('review_items')->nullOnDelete();
            $table->string('title');
            $table->text('action_plan');
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->date('due_date')->nullable();
            $table->string('status', 30)->default('pending'); // pending, in_progress, completed
            $table->timestamps();
            $table->softDeletes();

            $table->index(['review_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('improvements');
        Schema::dropIfExists('review_items');
        Schema::dropIfExists('reviews');
    }
};
