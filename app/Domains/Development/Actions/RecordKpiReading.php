<?php

declare(strict_types=1);

namespace App\Domains\Development\Actions;

use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\KpiReading;
use App\Models\User;

class RecordKpiReading
{
    public function handle(
        Kpi $kpi,
        float $value,
        User $recorder,
        ?string $notes = null,
        ?\DateTimeInterface $recordedAt = null
    ): KpiReading {
        return KpiReading::create([
            'kpi_id'      => $kpi->id,
            'value'       => $value,
            'notes'       => $notes,
            'recorded_at' => $recordedAt ?? now(),
            'created_by'  => $recorder->id,
        ]);
    }
}
