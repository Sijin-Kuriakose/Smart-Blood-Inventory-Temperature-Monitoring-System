<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodBankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'contact_number' => $this->contact_number,
            'refrigerators' => $this->whenLoaded('refrigerators', function () {
                return $this->refrigerators->map(function ($fridge) {
                    return [
                        'id' => $fridge->id,
                        'code' => $fridge->refrigerator_code,
                        'location' => $fridge->location,
                        'is_active' => $fridge->is_active,
                        'blood_bags_count' => $fridge->blood_bags_count ?? null,
                    ];
                });
            }),
            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                    ];
                });
            }),
            'total_blood_bags' => $this->whenCounted('bloodBags'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
