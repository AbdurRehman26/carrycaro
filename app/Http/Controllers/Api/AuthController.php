<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone_number' => $request->phone_number,
            'facebook_profile' => $request->facebook_profile,
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login with email and password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Login or register via a social provider.
     */
    public function socialLogin(SocialLoginRequest $request): JsonResponse
    {
        $user = User::firstWhere('email', $request->email);

        if ($user) {
            $user->update([
                $request->provider.'_id' => $request->provider_id,
                'profile_image' => $request->profile_image ?? $user->profile_image,
            ]);
        } else {
            $user = User::create([
                $request->provider.'_id' => $request->provider_id,
                'name' => $request->name,
                'email' => $request->email,
                'profile_image' => $request->profile_image,
                'password' => '',
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
