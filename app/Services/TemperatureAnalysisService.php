<?php

namespace App\Services;

use App\Models\TemperatureLog;
use Illuminate\Support\Facades\Log;

class TemperatureAnalysisService
{
    /**
     * Calculate daily risk analysis for a refrigerator.
     *
     * Analyzes temperature logs to determine risk percentage,
     * average/min/max temperatures, and unsafe reading counts.
     *
     * @param int $refrigeratorId
     * @param \Carbon\Carbon|null $date
     * @return array|null
     */
    public function calculateDailyRiskAnalysis($refrigeratorId, $date = null)
    {
        try {
            $date = $date ?? today();

            $logs = TemperatureLog::where('refrigerator_id', $refrigeratorId)
                ->whereDate('recorded_at', $date)
                ->orderBy('recorded_at')
                ->get();

            if ($logs->isEmpty()) {
                return null;
            }

            $totalMinutes = $logs->count();
            $unsafeMinutes = $logs->filter(function ($log) {
                return $log->temperature < 2 || $log->temperature > 6;
            })->count();

            return [
                'refrigerator_id' => $refrigeratorId,
                'date' => $date->toDateString(),
                'average_temperature' => round($logs->avg('temperature'), 2),
                'highest_temperature' => round($logs->max('temperature'), 2),
                'lowest_temperature' => round($logs->min('temperature'), 2),
                'total_minutes' => $totalMinutes,
                'unsafe_minutes' => $unsafeMinutes,
                'risk_percentage' => round(($unsafeMinutes / $totalMinutes) * 100, 2),
            ];
        } catch (\Exception $e) {
            Log::error('Daily risk analysis failed for refrigerator ' . $refrigeratorId . ': ' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Check if a refrigerator has critical temperature.
     *
     * Returns true if all of the last 10 readings
     * exceed 8°C — indicating a critical failure scenario.
     *
     * @param int $refrigeratorId
     * @return bool
     */
    public function checkCriticalTemperature($refrigeratorId)
    {
        try {
            $logs = TemperatureLog::where('refrigerator_id', $refrigeratorId)
                ->orderBy('recorded_at', 'desc')
                ->limit(10)
                ->get();

            if ($logs->count() < 10) {
                return false;
            }

            $allCritical = $logs->every(function ($log) {
                return $log->temperature > 8;
            });

            return $allCritical;
        } catch (\Exception $e) {
            Log::error('Critical temperature check failed for refrigerator ' . $refrigeratorId . ': ' . $e->getMessage());

            throw $e;
        }
    }
}
