<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodBagRequest;
use App\Http\Requests\UpdateBloodBagRequest;
use App\Http\Resources\BloodBagResource;
use App\Models\BloodBag;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BloodBagController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of blood bags with optional filters.
     *
     * Optimized with eager loading and active refrigerator filtering.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = BloodBag::with(['refrigerator' => function ($query) {
                    $query->with('bloodBank');
                }])
                ->whereHas('refrigerator', function ($query) {
                    $query->where('is_active', true);
                });

            // Filter by blood group
            if ($request->has('blood_group')) {
                $query->where('blood_group', $request->blood_group);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter expiring soon (within 24 hours)
            if ($request->has('expiring_soon')) {
                $query->where('expiry_date', '<=', now()->addDay())
                      ->where('status', 'available');
            }

            $bloodBags = $query->paginate(20);

            return $this->successResponse(
                BloodBagResource::collection($bloodBags)->response()->getData(true),
                'Blood bags retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve blood bags: ' . $e->getMessage());

            return $this->errorResponse('Failed to retrieve blood bags.', 500);
        }
    }

    /**
     * Store a newly created blood bag.
     */
    public function store(StoreBloodBagRequest $request): JsonResponse
    {
        try {
            $bloodBag = BloodBag::create($request->validated());

            return $this->createdResponse(
                new BloodBagResource($bloodBag->load('refrigerator')),
                'Blood bag created successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to create blood bag: ' . $e->getMessage());

            return $this->errorResponse('Failed to create blood bag.', 500);
        }
    }

    /**
     * Display the specified blood bag.
     */
    public function show(BloodBag $bloodBag): JsonResponse
    {
        try {
            return $this->successResponse(
                new BloodBagResource($bloodBag->load('refrigerator.bloodBank')),
                'Blood bag retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve blood bag: ' . $e->getMessage());

            return $this->errorResponse('Failed to retrieve blood bag.', 500);
        }
    }

    /**
     * Update the specified blood bag.
     */
    public function update(UpdateBloodBagRequest $request, BloodBag $bloodBag): JsonResponse
    {
        try {
            $bloodBag->update($request->validated());

            return $this->successResponse(
                new BloodBagResource($bloodBag->load('refrigerator')),
                'Blood bag updated successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to update blood bag: ' . $e->getMessage());

            return $this->errorResponse('Failed to update blood bag.', 500);
        }
    }

    /**
     * Remove the specified blood bag.
     */
    public function destroy(BloodBag $bloodBag): JsonResponse
    {
        try {
            $bloodBag->delete();

            return $this->successResponse(null, 'Blood bag deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete blood bag: ' . $e->getMessage());

            return $this->errorResponse('Failed to delete blood bag.', 500);
        }
    }
}
