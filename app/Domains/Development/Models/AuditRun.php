<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditRun extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    public const STATUS_SCHEDULED   = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    protected $fillable = [
        'tenant_id',
        'module_id',
        'audit_template_id',
        'title',
        'conducted_by',
        'due_date',
        'cadence',
        'status',
        'overall_score',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'overall_score' => 'float',
        'due_date'      => 'date',
        'completed_at'  => 'datetime',
    ];

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_COMPLETED && $this->due_date && $this->due_date->isPast();
    }

    public function getAuditorIdAttribute(): ?int
    {
        return $this->conducted_by;
    }

    public function nodeType(): NodeType
    {
        return NodeType::Audit;
    }

    public function nodeTitle(): string
    {
        return "Audit: {$this->template?->name} ({$this->status})";
    }

    public function nodeSummary(): ?string
    {
        return "Score: " . ($this->overall_score !== null ? "{$this->overall_score}%" : "Pending");
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AuditTemplate::class, 'audit_template_id');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AuditResponse::class);
    }
}
