<?php

declare(strict_types=1);

namespace App\Domains\Graph\Models;

use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class KnowledgeEdge extends Model
{
    use BelongsToTenant;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'relation',
        'strength',
        'meta',
        'created_by',
        'created_at',
        'invalidated_at',
        'invalidated_by',
        'invalidation_reason',
    ];

    protected $casts = [
        'source_type' => NodeType::class,
        'target_type' => NodeType::class,
        'relation'    => RelationType::class,
        'strength'    => 'integer',
        'meta'        => 'array',
        'created_at'  => 'datetime',
        'invalidated_at' => 'datetime',
    ];

    public function delete(): ?bool
    {
        throw new LogicException(
            'Knowledge edges can NEVER be deleted. Use invalidate() with a documented reason instead.'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invalidator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invalidated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('invalidated_at');
    }

    public function scopeInvalidated(Builder $query): Builder
    {
        return $query->whereNotNull('invalidated_at');
    }

    public function isActive(): bool
    {
        return $this->invalidated_at === null;
    }

    public function resolveSource(): ?KnowledgeNode
    {
        $class = $this->source_type->modelClass();
        return $class::find($this->source_id);
    }

    public function resolveTarget(): ?KnowledgeNode
    {
        $class = $this->target_type->modelClass();
        return $class::find($this->target_id);
    }

    public function invalidate(User $user, string $reason): void
    {
        if (!$this->isActive()) {
            throw new LogicException('Edge is already invalidated.');
        }

        $this->update([
            'invalidated_at'      => now(),
            'invalidated_by'      => $user->id,
            'invalidation_reason' => $reason,
        ]);
    }
}
