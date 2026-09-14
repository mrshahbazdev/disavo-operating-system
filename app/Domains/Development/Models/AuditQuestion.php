<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditQuestion extends Model
{
    protected $fillable = [
        'audit_template_id',
        'question_text',
        'guidance',
        'weight',
        'max_score',
        'order',
    ];

    protected $casts = [
        'weight'    => 'integer',
        'max_score' => 'integer',
        'order'     => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(AuditTemplate::class, 'audit_template_id');
    }
}
