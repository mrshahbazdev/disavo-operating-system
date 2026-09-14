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

class Goal extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    protected $fillable = [
        'tenant_id',
        'module_id',
        'title',
        'description',
        'target_score',
        'version',
        'created_by',
    ];

    protected $casts = [
        'target_score' => 'float',
        'version'      => 'integer',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Goal;
    }

    public function nodeTitle(): string
    {
        return $this->title;
    }

    public function nodeSummary(): ?string
    {
        return $this->description;
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
