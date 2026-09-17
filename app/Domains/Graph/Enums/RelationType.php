<?php

declare(strict_types=1);

namespace App\Domains\Graph\Enums;

enum RelationType: string
{
    // ALF — Knowledge Flow
    case Produces    = 'produces';      // Observation → Insight
    case Validates   = 'validates';     // Insight     → Learning
    case Promotes    = 'promotes';      // Learning    → Principle
    case Answers     = 'answers';       // Learning    → Question
    case Raises      = 'raises';        // Any         → Question

    // AMF — Development Flow
    case Governs     = 'governs';       // Principle   → Module
    case Measures    = 'measures';      // Audit       → Module
    case Quantifies  = 'quantifies';    // Kpi         → Module
    case Improves    = 'improves';      // Tool        → Module

    // ARF — Improvement Flow
    case Evaluates   = 'evaluates';     // Review      → Module | Principle
    case Generates   = 'generates';     // Review | Observation → Learning
    case Supersedes  = 'supersedes';    // Principle   → Principle
    case Contradicts = 'contradicts';   // Learning    → Principle (Tension Signal)

    // Decision Trail (Entscheidungspfad)
    case JustifiedBy = 'justified_by';  // Decision    → Principle

    /**
     * @return NodeType[]
     */
    public function allowedSources(): array
    {
        return match ($this) {
            self::Produces    => [NodeType::Observation],
            self::Validates   => [NodeType::Insight],
            self::Promotes    => [NodeType::Learning],
            self::Answers     => [NodeType::Learning],
            self::Raises      => NodeType::cases(), // Any node can raise a question
            self::Governs     => [NodeType::Principle],
            self::Measures    => [NodeType::Audit],
            self::Quantifies  => [NodeType::Kpi],
            self::Improves    => [NodeType::Tool],
            self::Evaluates   => [NodeType::Review],
            self::Generates   => [NodeType::Review, NodeType::Observation],
            self::Supersedes  => [NodeType::Principle],
            self::Contradicts => [NodeType::Learning],
            self::JustifiedBy => [NodeType::Decision],
        };
    }

    /**
     * @return NodeType[]
     */
    public function allowedTargets(): array
    {
        return match ($this) {
            self::Produces    => [NodeType::Insight],
            self::Validates   => [NodeType::Learning],
            self::Promotes    => [NodeType::Principle],
            self::Answers     => [NodeType::Question],
            self::Raises      => [NodeType::Question],
            self::Governs     => [NodeType::Module],
            self::Measures    => [NodeType::Module],
            self::Quantifies  => [NodeType::Module],
            self::Improves    => [NodeType::Module],
            self::Evaluates   => [NodeType::Module, NodeType::Principle],
            self::Generates   => [NodeType::Learning],
            self::Supersedes  => [NodeType::Principle],
            self::Contradicts => [NodeType::Principle],
            self::JustifiedBy => [NodeType::Principle],
        };
    }

    public function isValidConnection(NodeType $source, NodeType $target): bool
    {
        return in_array($source, $this->allowedSources(), true)
            && in_array($target, $this->allowedTargets(), true);
    }
}
