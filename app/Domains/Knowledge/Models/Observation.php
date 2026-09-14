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

class Observation extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    protected $fillable = [
        'tenant_id',
        'title',
        'content',
        'context',
        'created_by',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Observation;
    }

    public function nodeTitle(): string
    {
        return $this->title;
    }

    public function nodeSummary(): ?string
    {
        return $this->content;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
