<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Update authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->only([
            'name',
            'phone_number',
            'facebook_profile',
            'timezone',
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete the user
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}
