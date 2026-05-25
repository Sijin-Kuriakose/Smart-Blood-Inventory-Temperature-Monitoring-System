<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'refrigerator_id',
        'type',
        'message',
        'triggered_at',
        'is_resolved',
    ];

    protected function casts(): array
    {
        return [
            'triggered_at' => 'datetime',
            'is_resolved' => 'boolean',
        ];
    }

    /**
     * The refrigerator this alert belongs to.
     */
    public function refrigerator(): BelongsTo
    {
        return $this->belongsTo(Refrigerator::class);
    }

    /**
     * Scope: only unresolved alerts.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope: only critical alerts.
     */
    public function scopeCritical($query)
    {
        return $query->where('type', 'critical');
    }
}
