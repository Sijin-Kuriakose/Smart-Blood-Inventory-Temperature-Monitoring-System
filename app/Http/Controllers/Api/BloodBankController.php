<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodBankRequest;
use App\Http\Requests\UpdateBloodBankRequest;
use App\Http\Resources\BloodBankResource;
use App\Models\BloodBank;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BloodBankController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of blood banks.
     */
    public function index(): JsonResponse
    {
        try {
            $bloodBanks = BloodBank::with(['refrigerators', 'users'])->get();

            return $this->successResponse(
                BloodBankResource::collection($bloodBanks),
                'Blood banks retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve blood banks: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve blood banks.', 500);
        }
    }

    /**
     * Store a newly created blood bank.
     */
    public function store(StoreBloodBankRequest $request): JsonResponse
    {
        try {
            $bloodBank = BloodBank::create($request->validated());

            return $this->createdResponse(
                new BloodBankResource($bloodBank),
                'Blood bank created successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to create blood bank: ' . $e->getMessage());
            return $this->errorResponse('Failed to create blood bank.', 500);
        }
    }

    /**
     * Display the specified blood bank.
     */
    public function show(BloodBank $bloodBank): JsonResponse
    {
        try {
            return $this->successResponse(
                new BloodBankResource($bloodBank->load(['refrigerators', 'users'])),
                'Blood bank retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve blood bank: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve blood bank.', 500);
        }
    }

    /**
     * Update the specified blood bank.
     */
    public function update(UpdateBloodBankRequest $request, BloodBank $bloodBank): JsonResponse
    {
        try {
            $bloodBank->update($request->validated());

            return $this->successResponse(
                new BloodBankResource($bloodBank),
                'Blood bank updated successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to update blood bank: ' . $e->getMessage());
            return $this->errorResponse('Failed to update blood bank.', 500);
        }
    }

    /**
     * Remove the specified blood bank.
     */
    public function destroy(BloodBank $bloodBank): JsonResponse
    {
        try {
            $bloodBank->delete();

            return $this->successResponse(null, 'Blood bank deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete blood bank: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete blood bank.', 500);
        }
    }
}
