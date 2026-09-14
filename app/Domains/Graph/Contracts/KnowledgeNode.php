<?php

declare(strict_types=1);

namespace App\Domains\Graph\Contracts;

use App\Domains\Graph\Enums\NodeType;

interface KnowledgeNode
{
    public function nodeType(): NodeType;

    public function nodeTitle(): string;

    public function nodeSummary(): ?string;
}
