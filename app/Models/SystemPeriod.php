<?php
// app/Models/SystemPeriod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemPeriod extends Model
{
    const STATUS_OPEN = 'open';
    const STATUS_VALIDATION = 'validation';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'period',
        'status',
        'opened_at',
        'validation_started_at',
        'closed_at',
        'closed_by_user_id',
        'closure_summary',
        'is_current'
    ];

    protected $casts = [
        'opened_at' => 'date',
        'validation_started_at' => 'date',
        'closed_at' => 'date',
        'closure_summary' => 'array',
        'is_current' => 'boolean'
    ];

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public static function getCurrentPeriod(): ?self
    {
        return self::where('is_current', true)->first();
    }

    public function canBeClosed(): bool
    {
        return $this->status === self::STATUS_VALIDATION;
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }
}
