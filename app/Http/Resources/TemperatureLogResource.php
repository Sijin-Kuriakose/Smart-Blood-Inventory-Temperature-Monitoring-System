<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemperatureLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'refrigerator_id' => $this->refrigerator_id,
            'temperature' => $this->temperature,
            'recorded_at' => $this->recorded_at,
            'refrigerator' => $this->whenLoaded('refrigerator', function () {
                return [
                    'id' => $this->refrigerator->id,
                    'code' => $this->refrigerator->refrigerator_code,
                    'location' => $this->refrigerator->location,
                ];
            }),
        ];
    }
}
