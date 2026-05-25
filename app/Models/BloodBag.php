<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodBag extends Model
{
    use HasFactory;

    protected $fillable = [
        'refrigerator_id',
        'bag_number',
        'blood_group',
        'donor_name',
        'collection_date',
        'expiry_date',
        'quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'collection_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    /**
     * The refrigerator this blood bag is stored in.
     */
    public function refrigerator(): BelongsTo
    {
        return $this->belongsTo(Refrigerator::class);
    }

    /**
     * Mutator: auto-uppercase blood group.
     */
    protected function setBloodGroupAttribute($value)
    {
        $this->attributes['blood_group'] = strtoupper($value);
    }

    /**
     * Accessor: check if blood bag is expiring within 1 day.
     */
    public function getIsExpiringAttribute()
    {
        return $this->expiry_date <= now()->addDay();
    }
}
