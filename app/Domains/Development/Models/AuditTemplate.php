<?php

declare(strict_types=1);

namespace App\Domains\Development\Models;

use App\Domains\Support\Models\DosModel;
use App\Domains\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditTemplate extends DosModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'module_id',
        'name',
        'description',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AuditQuestion::class)->orderBy('order');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AuditRun::class);
    }
}
