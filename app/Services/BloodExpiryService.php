<?php

namespace App\Services;

use App\Models\BloodBag;
use Illuminate\Support\Facades\Log;

class BloodExpiryService
{
    /**
     * Get blood bags expiring within the next 24 hours.
     *
     * Returns only available bags that are not yet expired
     * but will expire within one day.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpiringSoon()
    {
        try {
            return BloodBag::where('expiry_date', '<=', now()->addDay())
                ->where('expiry_date', '>', now())
                ->where('status', 'available')
                ->with('refrigerator')
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to fetch expiring blood bags: ' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Get already expired blood bags that haven't been updated.
     *
     * Returns bags with past expiry dates still marked
     * as available or reserved (should be flagged/handled).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpired()
    {
        try {
            return BloodBag::where('expiry_date', '<', now())
                ->whereIn('status', ['available', 'reserved'])
                ->with('refrigerator')
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to fetch expired blood bags: ' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Calculate the percentage of available blood bags near expiry risk.
     *
     * "Near risk" = expiring within the next 3 days.
     * Returns 0 if no available bags exist (prevents division by zero).
     *
     * @return float
     */
    public function getNearRiskPercentage()
    {
        try {
            $total = BloodBag::where('status', 'available')->count();

            if ($total == 0) return 0;

            $nearRisk = BloodBag::where('status', 'available')
                ->where('expiry_date', '<=', now()->addDays(3))
                ->where('expiry_date', '>', now())
                ->count();

            return round(($nearRisk / $total) * 100, 2);
        } catch (\Exception $e) {
            Log::error('Failed to calculate near risk percentage: ' . $e->getMessage());

            throw $e;
        }
    }
}
