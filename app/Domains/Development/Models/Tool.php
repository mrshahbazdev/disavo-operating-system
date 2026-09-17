<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use App\Domains\Graph\Concerns\HasKnowledgeGraph;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tool extends DosModel implements KnowledgeNode
{
    use BelongsToTenant;
    use HasKnowledgeGraph;

    public const TYPE_CHECKLIST   = 'checklist';
    public const TYPE_TEMPLATE    = 'template';
    public const TYPE_WHITEPAPER  = 'whitepaper';
    public const TYPE_SAAS        = 'saas';
    public const TYPE_AI_FUNCTION = 'ai_function';

    protected $fillable = [
        'tenant_id',
        'module_id',
        'name',
        'type',
        'url_or_path',
        'description',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function nodeType(): NodeType
    {
        return NodeType::Tool;
    }

    public function nodeTitle(): string
    {
        return $this->name;
    }

    public function nodeSummary(): ?string
    {
        return "[{$this->type}] {$this->description}";
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
