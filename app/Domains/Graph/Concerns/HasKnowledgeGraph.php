<?php

declare(strict_types=1);

namespace App\Domains\Graph\Concerns;

use App\Domains\Graph\Actions\CreateEdge;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasKnowledgeGraph
{
    abstract public function nodeType(): \App\Domains\Graph\Enums\NodeType;

    public function outgoingEdges(): HasMany
    {
        return $this->hasMany(KnowledgeEdge::class, 'source_id')
            ->where('source_type', $this->nodeType()->value)
            ->whereNull('invalidated_at');
    }

    public function incomingEdges(): HasMany
    {
        return $this->hasMany(KnowledgeEdge::class, 'target_id')
            ->where('target_type', $this->nodeType()->value)
            ->whereNull('invalidated_at');
    }

    public function allOutgoingEdges(): HasMany
    {
        return $this->hasMany(KnowledgeEdge::class, 'source_id')
            ->where('source_type', $this->nodeType()->value);
    }

    public function allIncomingEdges(): HasMany
    {
        return $this->hasMany(KnowledgeEdge::class, 'target_id')
            ->where('target_type', $this->nodeType()->value);
    }

    public function linkTo(KnowledgeNode $target, RelationType $relation, array $meta = [], int $strength = 100): KnowledgeEdge
    {
        return app(CreateEdge::class)->handle($this, $target, $relation, $meta, $strength);
    }

    /**
     * Get outgoing resolved target nodes for a given relation.
     *
     * @return Collection<int, KnowledgeNode>
     */
    public function related(RelationType $relation): Collection
    {
        return $this->outgoingEdges()
            ->where('relation', $relation->value)
            ->get()
            ->map(fn (KnowledgeEdge $edge) => $edge->resolveTarget())
            ->filter()
            ->values();
    }

    /**
     * Get incoming resolved source nodes for a given relation.
     *
     * @return Collection<int, KnowledgeNode>
     */
    public function predecessors(RelationType $relation): Collection
    {
        return $this->incomingEdges()
            ->where('relation', $relation->value)
            ->get()
            ->map(fn (KnowledgeEdge $edge) => $edge->resolveSource())
            ->filter()
            ->values();
    }
}
