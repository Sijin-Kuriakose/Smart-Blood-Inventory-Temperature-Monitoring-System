<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $notifications = $request->user()->notifications()->paginate(20);

            return $this->successResponse(
                $notifications,
                'Notifications retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve notifications: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve notifications.', 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $notification = $request->user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return $this->successResponse(
                $notification,
                'Notification marked as read successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return $this->errorResponse('Failed to mark notification as read.', 500);
        }
    }
}
