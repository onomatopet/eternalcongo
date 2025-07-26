<?php
// app/Models/WorkflowLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowLog extends Model
{
    protected $fillable = [
        'period',
        'step',
        'action',
        'status',
        'user_id',
        'details',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes de statut
    const STATUS_STARTED = 'started';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_SKIPPED = 'skipped';

    // Constantes d'étapes
    const STEP_VALIDATION = 'validation';
    const STEP_AGGREGATION = 'aggregation';
    const STEP_ADVANCEMENT = 'advancement';
    const STEP_SNAPSHOT = 'snapshot';
    const STEP_CLOSURE = 'closure';

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accesseurs
    public function getDurationAttribute(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->completed_at->diffInSeconds($this->started_at);
        }
        return null;
    }

    public function getDurationForHumansAttribute(): ?string
    {
        $duration = $this->duration;
        if ($duration === null) {
            return null;
        }

        if ($duration < 60) {
            return $duration . ' secondes';
        } elseif ($duration < 3600) {
            return round($duration / 60, 1) . ' minutes';
        } else {
            return round($duration / 3600, 1) . ' heures';
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_STARTED => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_SKIPPED => 'gray',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_STARTED => 'En cours',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_FAILED => 'Échoué',
            self::STATUS_SKIPPED => 'Ignoré',
            default => 'Inconnu'
        };
    }

    public function getStepLabelAttribute(): string
    {
        return match($this->step) {
            self::STEP_VALIDATION => 'Validation des achats',
            self::STEP_AGGREGATION => 'Agrégation des achats',
            self::STEP_ADVANCEMENT => 'Calcul des avancements',
            self::STEP_SNAPSHOT => 'Création du snapshot',
            self::STEP_CLOSURE => 'Clôture de la période',
            default => $this->step
        };
    }

    // Méthodes statiques
    public static function logStart(string $period, string $step, string $action, int $userId, array $details = []): self
    {
        return self::create([
            'period' => $period,
            'step' => $step,
            'action' => $action,
            'status' => self::STATUS_STARTED,
            'user_id' => $userId,
            'details' => $details,
            'started_at' => now(),
        ]);
    }

    // Méthodes d'instance
    public function complete(array $additionalDetails = []): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'details' => array_merge($this->details ?? [], $additionalDetails),
        ]);
    }

    public function fail(string $errorMessage, array $additionalDetails = []): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'completed_at' => now(),
            'error_message' => $errorMessage,
            'details' => array_merge($this->details ?? [], $additionalDetails),
        ]);
    }

    public function skip(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_SKIPPED,
            'completed_at' => now(),
            'details' => array_merge($this->details ?? [], ['skip_reason' => $reason]),
        ]);
    }

    // Scopes
    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeForStep($query, string $step)
    {
        return $query->where('step', $step);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
