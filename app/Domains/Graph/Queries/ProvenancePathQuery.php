<?php

declare(strict_types=1);

namespace App\Domains\Graph\Queries;

use App\Domains\Graph\Enums\NodeType;
use App\Domains\Tenancy\Context\TenantContext;
use Illuminate\Support\Facades\DB;

class ProvenancePathQuery
{
    /**
     * Trace backwards from a target node (Decision, Principle, Learning) to its root origins (ALF Observations).
     *
     * @return array<int, object>
     */
    public function trace(NodeType $type, int $id, ?int $tenantId = null, int $maxDepth = 12): array
    {
        $tenantId = $tenantId ?? TenantContext::getTenantId() ?? 1;
        $driver = DB::getDriverName();

        // If starting at a Decision, anchor on its JustifiedBy outgoing edge to Principle
        $isDecision = ($type === NodeType::Decision);

        if ($driver === 'pgsql') {
            $anchorCondition = $isDecision
                ? "e.source_type = ? AND e.source_id = ?"
                : "e.target_type = ? AND e.target_id = ?";

            return DB::select(<<<SQL
                WITH RECURSIVE trail AS (
                    SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                           e.relation, 1 AS depth,
                           ARRAY[e.id] AS path
                    FROM knowledge_edges e
                    WHERE e.tenant_id = ?
                      AND {$anchorCondition}
                      AND e.invalidated_at IS NULL

                    UNION ALL

                    SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                           e.relation, t.depth + 1,
                           t.path || e.id
                    FROM knowledge_edges e
                    JOIN trail t
                      ON (
                          (t.relation = 'justified_by' AND e.target_type = t.target_type AND e.target_id = t.target_id AND e.id != t.id)
                          OR (t.relation != 'justified_by' AND e.target_type = t.source_type AND e.target_id = t.source_id)
                      )
                    WHERE e.tenant_id = ?
                      AND e.invalidated_at IS NULL
                      AND t.depth < ?
                      AND NOT e.id = ANY(t.path)          -- cycle guard, mandatory
                )
                SELECT * FROM trail ORDER BY depth;
            SQL, [$tenantId, $type->value, $id, $tenantId, $maxDepth]);
        }

        // SQLite / MySQL 8 compatible CTE with path string cycle guard
        $anchorCondition = $isDecision
            ? "e.source_type = ? AND e.source_id = ?"
            : "e.target_type = ? AND e.target_id = ?";

        return DB::select(<<<SQL
            WITH RECURSIVE trail AS (
                SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                       e.relation, 1 AS depth,
                       ',' || CAST(e.id AS TEXT) || ',' AS path
                FROM knowledge_edges e
                WHERE e.tenant_id = ?
                  AND {$anchorCondition}
                  AND e.invalidated_at IS NULL

                UNION ALL

                SELECT e.id, e.source_type, e.source_id, e.target_type, e.target_id,
                       e.relation, t.depth + 1,
                       t.path || CAST(e.id AS TEXT) || ',' AS path
                FROM knowledge_edges e
                JOIN trail t
                  ON (
                      (t.relation = 'justified_by' AND e.target_type = t.target_type AND e.target_id = t.target_id AND e.id != t.id)
                      OR (t.relation != 'justified_by' AND e.target_type = t.source_type AND e.target_id = t.source_id)
                  )
                WHERE e.tenant_id = ?
                  AND e.invalidated_at IS NULL
                  AND t.depth < ?
                  AND INSTR(t.path, ',' || CAST(e.id AS TEXT) || ',') = 0
            )
            SELECT * FROM trail ORDER BY depth;
        SQL, [$tenantId, $type->value, $id, $tenantId, $maxDepth]);
    }
}
