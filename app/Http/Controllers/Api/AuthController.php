<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'monitoring_user',
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->createdResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'User registered successfully');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            return $this->errorResponse('Registration failed. Please try again.', 500);
        }
    }

    /**
     * Authenticate user and issue token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->unauthorizedResponse('The provided credentials are incorrect.');
            }

            // Revoke previous tokens for single-device login (optional security measure)
            $user->tokens()->delete();

            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login successful');
        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());

            return $this->errorResponse('Login failed. Please try again.', 500);
        }
    }

    /**
     * Logout user and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());

            return $this->errorResponse('Logout failed. Please try again.', 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load('bloodBanks');

            return $this->successResponse($user, 'User profile retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch user profile: ' . $e->getMessage());

            return $this->errorResponse('Failed to fetch user profile.', 500);
        }
    }
}
