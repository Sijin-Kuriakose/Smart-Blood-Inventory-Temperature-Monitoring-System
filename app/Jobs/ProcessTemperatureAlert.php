<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Refrigerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTemperatureAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $refrigerator;
    protected $averageTemperature;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(Refrigerator $refrigerator, $averageTemperature)
    {
        $this->refrigerator = $refrigerator;
        $this->averageTemperature = $averageTemperature;
    }

    /**
     * Execute the job.
     *
     * Creates a critical alert record in the database for the refrigerator
     * that has exceeded safe temperature thresholds, and sends notifications.
     */
    public function handle(): void
    {
        try {
            $alert = Alert::create([
                'refrigerator_id' => $this->refrigerator->id,
                'type' => 'critical',
                'message' => "Critical temperature alert: {$this->averageTemperature}°C for 10 minutes",
                'triggered_at' => now(),
            ]);

            Log::info('Critical alert created for refrigerator ' . $this->refrigerator->id
                . ' with temperature ' . $this->averageTemperature . '°C');

            // Dispatch notifications to users associated with the refrigerator's blood bank
            $users = $this->refrigerator->bloodBank->users;
            if ($users && $users->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\CriticalTemperatureNotification($alert));
            }
        } catch (\Exception $e) {
            Log::error('Failed to create critical alert for refrigerator '
                . $this->refrigerator->id . ': ' . $e->getMessage());

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ProcessTemperatureAlert job permanently failed for refrigerator '
            . $this->refrigerator->id . ': ' . $exception->getMessage());
    }
}
