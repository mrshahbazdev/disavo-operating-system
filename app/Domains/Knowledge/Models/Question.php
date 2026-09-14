<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    public const STATUS_OPEN = 'open';
    public const STATUS_ANSWERED = 'answered';
    public const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'tenant_id',
        'title',
        'context',
        'status',
        'answered_at',
        'answered_by',
        'created_by',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Question;
    }

    public function nodeTitle(): string
    {
        return $this->title;
    }

    public function nodeSummary(): ?string
    {
        return $this->context;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function answeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }
}
