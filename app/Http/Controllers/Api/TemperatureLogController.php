<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTemperatureLogRequest;
use App\Http\Resources\TemperatureLogResource;
use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TemperatureLogController extends Controller
{
    use ApiResponseTrait;

    /**
     * Store a newly created temperature log.
     */
    public function store(StoreTemperatureLogRequest $request): JsonResponse
    {
        try {
            $log = TemperatureLog::create($request->validated());

            return $this->createdResponse(
                new TemperatureLogResource($log),
                'Temperature log created successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to create temperature log: ' . $e->getMessage());
            return $this->errorResponse('Failed to create temperature log.', 500);
        }
    }

    /**
     * Display temperature logs for a specific refrigerator.
     */
    public function indexForRefrigerator(Refrigerator $refrigerator, Request $request): JsonResponse
    {
        try {
            $query = TemperatureLog::where('refrigerator_id', $refrigerator->id)
                ->orderBy('recorded_at', 'desc');

            // Optional filter by date
            if ($request->has('date')) {
                $query->whereDate('recorded_at', $request->date);
            }

            $logs = $query->paginate(20);

            return $this->successResponse(
                TemperatureLogResource::collection($logs)->response()->getData(true),
                'Temperature logs retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve temperature logs for refrigerator ' . $refrigerator->id . ': ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve temperature logs.', 500);
        }
    }
}
