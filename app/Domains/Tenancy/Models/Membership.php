<?php

declare(strict_types=1);

namespace App\Domains\Tenancy\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    protected $table = 'tenant_memberships';

    public const ROLE_OWNER = 'owner';
    public const ROLE_STEWARD = 'steward';
    public const ROLE_CONTRIBUTOR = 'contributor';
    public const ROLE_OBSERVER = 'observer';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
