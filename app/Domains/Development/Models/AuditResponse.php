<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditResponse extends Model
{
    protected $fillable = [
        'audit_run_id',
        'audit_question_id',
        'score',
        'status_symbol',
        'binary_answer',
        'notes',
        'evidence',
    ];

    protected $casts = [
        'score'         => 'integer',
        'binary_answer' => 'boolean',
    ];

    public function isUsable(): bool
    {
        return $this->status_symbol === 'usable' || $this->status_symbol === '✅';
    }

    public function needsImprovement(): bool
    {
        return $this->status_symbol === 'improvement' || $this->status_symbol === '⚠️';
    }

    public function isCriticalDefect(): bool
    {
        return $this->status_symbol === 'defect' || $this->status_symbol === '❌';
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(AuditRun::class, 'audit_run_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AuditQuestion::class, 'audit_question_id');
    }
}
