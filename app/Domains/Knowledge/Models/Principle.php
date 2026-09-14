<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Knowledge\States\Principle\PrincipleState;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\ModelStates\HasStates;

class Principle extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;
    use HasStates;

    protected $fillable = [
        'tenant_id',
        'title',
        'statement',
        'rationale',
        'state',
        'version',
        'superseded_by_id',
        'retired_at',
        'retired_by',
        'retirement_reason',
        'created_by',
    ];

    protected $casts = [
        'state'      => PrincipleState::class,
        'version'    => 'integer',
        'retired_at' => 'datetime',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Principle;
    }

    public function nodeTitle(): string
    {
        return $this->title;
    }

    public function nodeSummary(): ?string
    {
        return $this->statement;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function retiredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retired_by');
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }

    public function previousVersions(): HasMany
    {
        return $this->hasMany(self::class, 'superseded_by_id');
    }
}
