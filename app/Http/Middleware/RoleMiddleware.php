<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more roles: middleware('role:admin,blood_bank_staff')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please provide a valid token.',
                ], 401);
            }

            if (!in_array($request->user()->role, $roles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Required role(s): ' . implode(', ', $roles),
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization check failed.',
            ], 500);
        }
    }
}
