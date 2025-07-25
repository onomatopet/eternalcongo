<?php
// app/Models/BonusThreshold.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusThreshold extends Model
{
    protected $fillable = [
        'grade',
        'minimum_pv',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'minimum_pv' => 'integer',
        'grade' => 'integer'
    ];

    public static function getMinimumPvForGrade(int $grade): int
    {
        return self::where('grade', $grade)
                   ->where('is_active', true)
                   ->value('minimum_pv') ?? 0;
    }
}
