<?php

declare(strict_types=1);

namespace App\Domains\Graph\Actions;

use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class CreateEdge
{
    /**
     * Create and validate a directed edge between two knowledge nodes.
     *
     * @param (KnowledgeNode&Model) $source
     * @param (KnowledgeNode&Model) $target
     */
    public function handle(
        KnowledgeNode $source,
        KnowledgeNode $target,
        RelationType $relation,
        array $meta = [],
        int $strength = 100,
        ?int $createdBy = null
    ): KnowledgeEdge {
        if (!$source instanceof Model || !$target instanceof Model) {
            throw new InvalidArgumentException('Both source and target must be Eloquent models.');
        }

        $sourceType = $source->nodeType();
        $targetType = $target->nodeType();

        if (!$relation->isValidConnection($sourceType, $targetType)) {
            throw new InvalidArgumentException(
                "Invalid edge: Relation '{$relation->value}' is not permitted between " .
                "source '{$sourceType->value}' and target '{$targetType->value}'."
            );
        }

        if ($source->tenant_id !== $target->tenant_id) {
            throw new InvalidArgumentException(
                'Cross-tenant edges are strictly forbidden. Source and target must belong to the same tenant.'
            );
        }

        $creatorId = $createdBy ?? auth()->id() ?? $source->created_by ?? 1;

        // Check for existing active edge
        $existing = KnowledgeEdge::withoutGlobalScopes()
            ->where('tenant_id', $source->tenant_id)
            ->where('source_type', $sourceType->value)
            ->where('source_id', $source->getKey())
            ->where('target_type', $targetType->value)
            ->where('target_id', $target->getKey())
            ->where('relation', $relation->value)
            ->whereNull('invalidated_at')
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return KnowledgeEdge::create([
            'tenant_id'   => $source->tenant_id,
            'source_type' => $sourceType,
            'source_id'   => $source->getKey(),
            'target_type' => $targetType,
            'target_id'   => $target->getKey(),
            'relation'    => $relation,
            'strength'    => max(0, min(100, $strength)),
            'meta'        => $meta,
            'created_by'  => $creatorId,
            'created_at'  => now(),
        ]);
    }
}
