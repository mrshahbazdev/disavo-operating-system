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
        'notes',
        'evidence',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(AuditRun::class, 'audit_run_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AuditQuestion::class, 'audit_question_id');
    }
}
