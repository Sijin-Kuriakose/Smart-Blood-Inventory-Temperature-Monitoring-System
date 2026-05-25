<?php

namespace App\Listeners;

use App\Events\CriticalTemperatureDetected;
use App\Jobs\ProcessTemperatureAlert;
use Illuminate\Support\Facades\Log;

class ProcessCriticalTemperatureAlert
{
    /**
     * Handle the event.
     *
     * Dispatches a queued job to create a critical alert record
     * when sustained high temperature is detected.
     */
    public function handle(CriticalTemperatureDetected $event): void
    {
        try {
            ProcessTemperatureAlert::dispatch(
                $event->refrigerator,
                $event->averageTemperature
            );
        } catch (\Exception $e) {
            Log::error('Failed to dispatch temperature alert job for refrigerator '
                . $event->refrigerator->id . ': ' . $e->getMessage());
        }
    }
}
