<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiReading extends Model
{
    protected $fillable = [
        'kpi_id',
        'value',
        'notes',
        'recorded_at',
        'created_by',
    ];

    protected $casts = [
        'value'       => 'float',
        'recorded_at' => 'datetime',
    ];

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
