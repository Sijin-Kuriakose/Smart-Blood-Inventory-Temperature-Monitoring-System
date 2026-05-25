<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\TemperatureLogObserver;

#[ObservedBy([TemperatureLogObserver::class])]
class TemperatureLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'refrigerator_id',
        'temperature',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * The refrigerator this temperature log belongs to.
     */
    public function refrigerator(): BelongsTo
    {
        return $this->belongsTo(Refrigerator::class);
    }
}
