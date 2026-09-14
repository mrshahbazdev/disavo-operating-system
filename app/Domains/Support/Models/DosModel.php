<?php

declare(strict_types=1);

namespace App\Domains\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

abstract class DosModel extends Model
{
    use SoftDeletes;
    use LogsActivity;

    /**
     * DOS Core Rule: Knowledge must never be lost. Hard deletes are prohibited.
     */
    public function forceDelete(): ?bool
    {
        if (!app()->runningUnitTests()) {
            throw new LogicException(
                'Hard deletes are strictly prohibited in DOS. Institutional memory must never be destroyed.'
            );
        }

        return parent::forceDelete();
    }

    /**
     * Immutable audit trail for every model change.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getTable());
    }
}
