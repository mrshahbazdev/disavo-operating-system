<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Module extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;
    use HasRecursiveRelationships;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'order',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Module;
    }

    public function nodeTitle(): string
    {
        return $this->name;
    }

    public function nodeSummary(): ?string
    {
        return $this->description;
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function auditTemplates(): HasMany
    {
        return $this->hasMany(AuditTemplate::class);
    }

    public function auditRuns(): HasMany
    {
        return $this->hasMany(AuditRun::class);
    }

    public function kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }
}
