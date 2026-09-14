<?php

declare(strict_types=1);

namespace App\Domains\Tenancy\Models;

use App\Domains\Support\Models\DosModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends DosModel
{
    protected $fillable = [
        'name',
        'slug',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_memberships')
            ->withPivot('role')
            ->withTimestamps();
    }
}
