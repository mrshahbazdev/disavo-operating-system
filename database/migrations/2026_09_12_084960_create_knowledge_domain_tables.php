<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Observations
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->json('context')->nullable(); // source, date observed, participants, etc.
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'created_at']);
        });

        // 2. Insights
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('summary');
            $table->text('implications')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'created_at']);
        });

        // 3. Learnings
        Schema::create('learnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('summary');
            $table->text('rationale')->nullable();
            $table->string('state', 30)->default('draft'); // LearningState
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('promoted_at')->nullable();
            $table->foreignId('promoted_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'state']);
            $table->index(['tenant_id', 'created_at']);
        });

        // 4. Principles
        Schema::create('principles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('statement'); // The actionable non-negotiable rule
            $table->text('rationale')->nullable();
            $table->string('state', 30)->default('proposed'); // PrincipleState
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('superseded_by_id')->nullable()->constrained('principles');
            $table->timestamp('retired_at')->nullable();
            $table->foreignId('retired_by')->nullable()->constrained('users');
            $table->text('retirement_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'state']);
            $table->index(['tenant_id', 'created_at']);
        });

        // 5. Questions (Offene Fragen)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('context')->nullable();
            $table->string('status', 30)->default('open'); // open, answered, dismissed
            $table->timestamp('answered_at')->nullable();
            $table->foreignId('answered_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'created_at']);
        });

        // 6. Decisions (Entscheidungspfad)
        Schema::create('decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('summary');
            $table->text('rationale')->nullable();
            $table->timestamp('decided_at')->useCurrent();
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'decided_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decisions');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('principles');
        Schema::dropIfExists('learnings');
        Schema::dropIfExists('insights');
        Schema::dropIfExists('observations');
    }
};
