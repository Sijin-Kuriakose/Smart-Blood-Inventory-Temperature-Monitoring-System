<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\BloodBag;
use App\Models\Refrigerator;
use App\Services\BloodExpiryService;
use App\Services\TemperatureAnalysisService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    use ApiResponseTrait;

    protected $temperatureService;
    protected $expiryService;

    public function __construct(
        TemperatureAnalysisService $temperatureService,
        BloodExpiryService $expiryService
    ) {
        $this->temperatureService = $temperatureService;
        $this->expiryService = $expiryService;
    }

    /**
     * Dashboard overview with stock, alerts, and risk metrics.
     *
     * Cached for 5 minutes (300 seconds) to reduce DB load.
     */
    public function dashboard(): JsonResponse
    {
        try {
            $data = Cache::remember('dashboard_stats', 300, function () {
                $stockByBloodGroup = BloodBag::where('status', 'available')
                    ->select('blood_group', DB::raw('SUM(quantity) as total_quantity'))
                    ->groupBy('blood_group')
                    ->get()
                    ->pluck('total_quantity', 'blood_group');

                $activeRefrigerators = Refrigerator::where('is_active', true)
                    ->withCount('bloodBags')
                    ->with('bloodBank')
                    ->get();

                $averageTempToday = \App\Models\TemperatureLog::whereDate('recorded_at', today())
                    ->avg('temperature');

                return [
                    'total_blood_bags' => BloodBag::count(),
                    'available_stock_by_blood_group' => $stockByBloodGroup,
                    'total_expired_bags' => BloodBag::where('status', 'expired')->count(),
                    'active_refrigerators' => $activeRefrigerators,
                    'average_temperature_today' => $averageTempToday ? round($averageTempToday, 2) : null,
                    'critical_alerts_today' => Alert::whereDate('triggered_at', today())
                        ->where('type', 'critical')
                        ->count(),
                    'expiring_within_24h' => $this->expiryService->getExpiringSoon()->count(),
                    'near_risk_percentage' => $this->expiryService->getNearRiskPercentage(),
                ];
            });

            return $this->successResponse($data, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to load dashboard: ' . $e->getMessage());

            return $this->errorResponse('Failed to load dashboard data.', 500);
        }
    }

    /**
     * Daily temperature risk analysis for a specific refrigerator.
     *
     * Cached per refrigerator for 2 minutes (120 seconds).
     */
    public function refrigeratorAnalysis($refrigeratorId): JsonResponse
    {
        try {
            $cacheKey = "refrigerator_analysis_{$refrigeratorId}_" . today()->toDateString();

            $analysis = Cache::remember($cacheKey, 120, function () use ($refrigeratorId) {
                return $this->temperatureService->calculateDailyRiskAnalysis($refrigeratorId);
            });

            if (is_null($analysis)) {
                return $this->successResponse(null, 'No temperature logs found for today.');
            }

            return $this->successResponse($analysis, 'Refrigerator analysis retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to analyze refrigerator ' . $refrigeratorId . ': ' . $e->getMessage());

            return $this->errorResponse('Failed to retrieve refrigerator analysis.', 500);
        }
    }
}
