<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodBagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'bag_number' => $this->bag_number,
            'blood_group' => $this->blood_group,
            'donor_name' => $this->donor_name,
            'collection_date' => $this->collection_date,
            'expiry_date' => $this->expiry_date,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'is_expiring' => $this->is_expiring,
            'refrigerator' => [
                'id' => $this->refrigerator->id,
                'code' => $this->refrigerator->refrigerator_code,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
