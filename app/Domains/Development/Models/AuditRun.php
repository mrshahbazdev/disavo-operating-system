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

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    protected $fillable = [
        'tenant_id',
        'module_id',
        'audit_template_id',
        'conducted_by',
        'status',
        'overall_score',
        'completed_at',
    ];

    protected $casts = [
        'overall_score' => 'float',
        'completed_at'  => 'datetime',
    ];

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
