<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('source_type', 40);   // NodeType value
            $table->unsignedBigInteger('source_id');
            $table->string('target_type', 40);
            $table->unsignedBigInteger('target_id');

            $table->string('relation', 40);      // RelationType value
            $table->unsignedTinyInteger('strength')->default(100); // 0–100 confidence

            $table->json('meta')->nullable();   // rationale, source excerpt, context
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('invalidated_at')->nullable();   // append-only: never hard-deleted
            $table->foreignId('invalidated_by')->nullable()->constrained('users');
            $table->text('invalidation_reason')->nullable();

            $table->unique(
                ['tenant_id', 'source_type', 'source_id', 'target_type', 'target_id', 'relation'],
                'knowledge_edges_unique'
            );
            $table->index(['tenant_id', 'source_type', 'source_id', 'invalidated_at'], 'edges_out_idx');
            $table->index(['tenant_id', 'target_type', 'target_id', 'invalidated_at'], 'edges_in_idx');
            $table->index(['tenant_id', 'relation'], 'edges_relation_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_edges');
    }
};
