<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Knowledge\States\Learning\LearningState;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\ModelStates\HasStates;

class Learning extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;
    use HasStates;

    protected $fillable = [
        'tenant_id',
        'title',
        'summary',
        'rationale',
        'state',
        'validated_at',
        'validated_by',
        'promoted_at',
        'promoted_by',
        'created_by',
    ];

    protected $casts = [
        'state'        => LearningState::class,
        'validated_at' => 'datetime',
        'promoted_at'  => 'datetime',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Learning;
    }

    public function nodeTitle(): string
    {
        return $this->title;
    }

    public function nodeSummary(): ?string
    {
        return $this->summary;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function promoter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }
}
