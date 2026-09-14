<?php

declare(strict_types=1);

namespace App\Domains\Review\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    public const STATUS_OPEN   = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'tenant_id',
        'title',
        'period',
        'review_date',
        'status',
        'summary',
        'created_by',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'review_date' => 'date',
        'closed_at'   => 'datetime',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Review;
    }

    public function nodeTitle(): string
    {
        return "Review: {$this->title} ({$this->period})";
    }

    public function nodeSummary(): ?string
    {
        return $this->summary;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReviewItem::class);
    }

    public function improvements(): HasMany
    {
        return $this->hasMany(Improvement::class);
    }
}
