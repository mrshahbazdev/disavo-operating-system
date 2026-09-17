<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_questions', function (Blueprint $table) {
            $table->string('area', 100)->nullable()->after('audit_template_id');
            $table->string('type', 30)->default('rating')->after('area'); // rating, binary, metric
            $table->string('section_header', 255)->nullable()->after('type');
        });

        Schema::table('audit_runs', function (Blueprint $table) {
            $table->string('version', 50)->default('1.0')->after('title');
            $table->unsignedInteger('user_paths_audited')->default(1)->after('version');
            $table->unsignedInteger('tools_audited')->default(0)->after('user_paths_audited');
            $table->decimal('previous_score', 5, 2)->nullable()->after('tools_audited');
            $table->boolean('entrepreneur_test_passed')->default(false)->after('previous_score');
            $table->boolean('new_feature_ban')->default(false)->after('entrepreneur_test_passed');
            $table->json('meta')->nullable()->after('notes');
        });

        Schema::table('audit_responses', function (Blueprint $table) {
            $table->string('status_symbol', 20)->nullable()->after('score'); // usable (✅), improvement (⚠️), defect (❌)
            $table->boolean('binary_answer')->nullable()->after('status_symbol'); // YES (true) / NO (false)
        });
    }

    public function down(): void
    {
        Schema::table('audit_responses', function (Blueprint $table) {
            $table->dropColumn(['status_symbol', 'binary_answer']);
        });

        Schema::table('audit_runs', function (Blueprint $table) {
            $table->dropColumn([
                'version',
                'user_paths_audited',
                'tools_audited',
                'previous_score',
                'entrepreneur_test_passed',
                'new_feature_ban',
                'meta',
            ]);
        });

        Schema::table('audit_questions', function (Blueprint $table) {
            $table->dropColumn(['area', 'type', 'section_header']);
        });
    }
};
