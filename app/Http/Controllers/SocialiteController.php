<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(string $provider): JsonResponse
    {
        $this->validateProvider($provider);

        $response = Socialite::driver($provider)->stateless()->user();

        $user = User::firstWhere(['email' => $response->getEmail()]);

        if ($user) {
            $user->update([$provider.'_id' => $response->getId()]);
        } else {
            User::create([
                $provider.'_id' => $response->getId(),
                'name' => $response->getName(),
                'email' => $response->getEmail(),
                'profile_image' => $response->getAvatar(),
                'password' => '',
                'email_verified_at' => now(),
            ]);

            $user = User::firstWhere(['email' => $response->getEmail()]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    protected function validateProvider(string $provider): array
    {
        return \Illuminate\Support\Facades\Validator::make(
            ['provider' => $provider],
            ['provider' => 'in:google']
        )->validate();
    }
}
