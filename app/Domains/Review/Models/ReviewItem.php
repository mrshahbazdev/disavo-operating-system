<?php

declare(strict_types=1);

namespace App\Domains\Review\Models;

use App\Domains\Development\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewItem extends Model
{
    protected $fillable = [
        'review_id',
        'module_id',
        'topic',
        'status',
        'notes',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function improvements(): HasMany
    {
        return $this->hasMany(Improvement::class);
    }
}
