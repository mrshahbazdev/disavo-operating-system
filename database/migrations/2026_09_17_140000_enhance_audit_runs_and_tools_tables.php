<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_runs', function (Blueprint $table) {
            $table->string('title')->nullable()->after('audit_template_id');
            $table->date('due_date')->nullable()->after('conducted_by');
            $table->string('cadence', 50)->nullable()->after('due_date');
            $table->text('notes')->nullable()->after('overall_score');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('description');
            $table->boolean('is_active')->default(true)->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['content', 'is_active']);
        });

        Schema::table('audit_runs', function (Blueprint $table) {
            $table->dropColumn(['title', 'due_date', 'cadence', 'notes']);
        });
    }
};
