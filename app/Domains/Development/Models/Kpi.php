<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kpi extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    protected $fillable = [
        'tenant_id',
        'module_id',
        'name',
        'code',
        'unit',
        'direction',
        'target_value',
    ];

    protected $casts = [
        'target_value' => 'float',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Kpi;
    }

    public function nodeTitle(): string
    {
        return $this->name;
    }

    public function nodeSummary(): ?string
    {
        return "Target: {$this->target_value} {$this->unit}";
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(KpiReading::class)->orderByDesc('recorded_at');
    }

    public function latestReading(): ?KpiReading
    {
        return $this->readings()->first();
    }
}
