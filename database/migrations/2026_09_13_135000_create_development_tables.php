<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modules (AMF)
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'parent_id']);
        });

        // 2. Goals (Zielzustand)
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_score', 5, 2)->default(100.00); // Target maturity 0-100%
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'module_id']);
        });

        // 3. Audit Templates
        Schema::create('audit_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Audit Questions (Weights in database, not hardcoded!)
        Schema::create('audit_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_template_id')->constrained('audit_templates')->cascadeOnDelete();
            $table->text('question_text');
            $table->text('guidance')->nullable();
            $table->unsignedTinyInteger('weight')->default(10); // DB-driven weight
            $table->unsignedTinyInteger('max_score')->default(5);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // 5. Audit Runs
        Schema::create('audit_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('audit_template_id')->constrained('audit_templates')->cascadeOnDelete();
            $table->foreignId('conducted_by')->constrained('users');
            $table->string('status', 30)->default('in_progress'); // in_progress, completed
            $table->decimal('overall_score', 5, 2)->nullable(); // 0-100%
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'module_id']);
            $table->index(['module_id', 'completed_at']);
        });

        // 6. Audit Responses
        Schema::create('audit_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_run_id')->constrained('audit_runs')->cascadeOnDelete();
            $table->foreignId('audit_question_id')->constrained('audit_questions')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(0);
            $table->text('notes')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();

            $table->unique(['audit_run_id', 'audit_question_id']);
        });

        // 7. KPIs (Definitions)
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50);
            $table->string('unit', 30)->nullable();
            $table->string('direction', 20)->default('higher_is_better'); // higher_is_better, lower_is_better
            $table->decimal('target_value', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'module_id']);
        });

        // 8. KPI Readings (Time-series, one row per measurement + recorded_at index)
        Schema::create('kpi_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->cascadeOnDelete();
            $table->decimal('value', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Missing-index fix upfront: composite index on kpi_id and recorded_at
            $table->index(['kpi_id', 'recorded_at']);
        });

        // 9. Tools (Werkzeuge)
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 30); // checklist, template, whitepaper, saas, ai_function
            $table->text('url_or_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tools');
        Schema::dropIfExists('kpi_readings');
        Schema::dropIfExists('kpis');
        Schema::dropIfExists('audit_responses');
        Schema::dropIfExists('audit_runs');
        Schema::dropIfExists('audit_questions');
        Schema::dropIfExists('audit_templates');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('modules');
    }
};
