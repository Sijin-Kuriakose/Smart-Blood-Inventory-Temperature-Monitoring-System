<?php

namespace App\Events;

use App\Models\Refrigerator;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CriticalTemperatureDetected
{
    use Dispatchable, SerializesModels;

    public $refrigerator;
    public $averageTemperature;

    /**
     * Create a new event instance.
     *
     * Fired when a refrigerator's temperature exceeds safe thresholds
     * for a sustained period, indicating potential blood supply risk.
     *
     * @param Refrigerator $refrigerator
     * @param float $averageTemperature
     */
    public function __construct(Refrigerator $refrigerator, $averageTemperature)
    {
        $this->refrigerator = $refrigerator;
        $this->averageTemperature = $averageTemperature;
    }
}
