<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class BloodBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'contact_number',
    ];

    /**
     * The users that belong to this blood bank.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * The refrigerators in this blood bank.
     */
    public function refrigerators(): HasMany
    {
        return $this->hasMany(Refrigerator::class);
    }

    /**
     * All blood bags across all refrigerators in this blood bank.
     */
    public function bloodBags(): HasManyThrough
    {
        return $this->hasManyThrough(BloodBag::class, Refrigerator::class);
    }
}
