<?php

declare(strict_types=1);

namespace App\Domains\Graph\Queries;

use App\Domains\Graph\Enums\NodeType;
use App\Domains\Tenancy\Context\TenantContext;
use Illuminate\Support\Facades\DB;

class ImpactRadiusQuery
{
    /**
     * Trace forward from a source node to all downstream nodes that depend on it.
     *
     * @return array<int, object>
     */
    public function trace(NodeType $type, int $id, ?int $tenantId = null, int $maxDepth = 12): array
    {
        $tenantId = $tenantId ?? TenantContext::getTenantId() ?? 1;
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return DB::select(<<<'SQL'
                WITH RECURSIVE downstream AS (
                    SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                           e.relation, 1 AS depth,
                           ARRAY[e.id] AS path
                    FROM knowledge_edges e
                    WHERE e.tenant_id = ?
                      AND e.source_type = ? AND e.source_id = ?
                      AND e.invalidated_at IS NULL

                    UNION ALL

                    SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                           e.relation, d.depth + 1,
                           d.path || e.id
                    FROM knowledge_edges e
                    JOIN downstream d
                      ON e.source_type = d.target_type
                     AND e.source_id   = d.target_id
                    WHERE e.tenant_id = ?
                      AND e.invalidated_at IS NULL
                      AND d.depth < ?
                      AND NOT e.id = ANY(d.path)
                )
                SELECT * FROM downstream ORDER BY depth;
            SQL, [$tenantId, $type->value, $id, $tenantId, $maxDepth]);
        }

        return DB::select(<<<'SQL'
            WITH RECURSIVE downstream AS (
                SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                       e.relation, 1 AS depth,
                       ',' || CAST(e.id AS TEXT) || ',' AS path
                FROM knowledge_edges e
                WHERE e.tenant_id = ?
                  AND e.source_type = ? AND e.source_id = ?
                  AND e.invalidated_at IS NULL

                UNION ALL

                SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                       e.relation, d.depth + 1,
                       d.path || CAST(e.id AS TEXT) || ',' AS path
                FROM knowledge_edges e
                JOIN downstream d
                  ON e.source_type = d.target_type
                 AND e.source_id   = d.target_id
                WHERE e.tenant_id = ?
                  AND e.invalidated_at IS NULL
                  AND d.depth < ?
                  AND INSTR(d.path, ',' || CAST(e.id AS TEXT) || ',') = 0
            )
            SELECT * FROM downstream ORDER BY depth;
        SQL, [$tenantId, $type->value, $id, $tenantId, $maxDepth]);
    }
}
