<?php

namespace App\Observers;

use App\Events\CriticalTemperatureDetected;
use App\Models\TemperatureLog;
use App\Services\TemperatureAnalysisService;
use Illuminate\Support\Facades\Log;

class TemperatureLogObserver
{
    protected $temperatureService;

    public function __construct(TemperatureAnalysisService $temperatureService)
    {
        $this->temperatureService = $temperatureService;
    }

    /**
     * Handle the TemperatureLog "created" event.
     *
     * Checks if the last 10 readings exceed critical threshold (8°C).
     * If so, fires CriticalTemperatureDetected event to create an alert.
     */
    public function created(TemperatureLog $log): void
    {
        try {
            if ($this->temperatureService->checkCriticalTemperature($log->refrigerator_id)) {
                event(new CriticalTemperatureDetected(
                    $log->refrigerator,
                    $log->temperature
                ));

                Log::warning('Critical temperature detected for refrigerator ' . $log->refrigerator_id
                    . ' — temperature: ' . $log->temperature . '°C');
            }
        } catch (\Exception $e) {
            Log::error('Temperature observer failed for log ' . $log->id . ': ' . $e->getMessage());
        }
    }
}
