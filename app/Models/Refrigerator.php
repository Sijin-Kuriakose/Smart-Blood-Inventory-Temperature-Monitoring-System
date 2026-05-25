<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refrigerator extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_bank_id',
        'refrigerator_code',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The blood bank this refrigerator belongs to.
     */
    public function bloodBank(): BelongsTo
    {
        return $this->belongsTo(BloodBank::class);
    }

    /**
     * The blood bags stored in this refrigerator.
     */
    public function bloodBags(): HasMany
    {
        return $this->hasMany(BloodBag::class);
    }

    /**
     * The temperature logs for this refrigerator.
     */
    public function temperatureLogs(): HasMany
    {
        return $this->hasMany(TemperatureLog::class);
    }

    /**
     * The alerts for this refrigerator.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Health score based on today's temperature readings within safe range (2-6°C).
     */
    public function getHealthScoreAttribute()
    {
        $logs = $this->temperatureLogs()
            ->whereDate('recorded_at', today())
            ->get();

        if ($logs->isEmpty()) return 100;

        $safeCount = $logs->whereBetween('temperature', [2, 6])->count();
        return round(($safeCount / $logs->count()) * 100, 2);
    }
}
