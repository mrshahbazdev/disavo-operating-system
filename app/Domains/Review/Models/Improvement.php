<?php

declare(strict_types=1);

namespace App\Domains\Review\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Improvement extends DosModel implements KnowledgeNode
{
    use HasKnowledgeGraph;

    public const STATUS_PENDING     = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    protected $fillable = [
        'review_id',
        'review_item_id',
        'title',
        'action_plan',
        'owner_id',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Improvement;
    }

    public function nodeTitle(): string
    {
        return $this->title;
    }

    public function nodeSummary(): ?string
    {
        return $this->action_plan;
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ReviewItem::class, 'review_item_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
