<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class GoogleIdTokenVerifier
{
    /**
     * @return array{sub: string, email: string, name?: string, picture?: string}
     */
    public function verify(string $idToken): array
    {
        $response = Http::timeout(5)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google ID token is invalid.'],
            ]);
        }

        $claims = $response->json();
        $clientIds = config('services.google.client_ids', []);

        if (! isset($claims['sub'], $claims['email'], $claims['aud'])) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google ID token is missing required claims.'],
            ]);
        }

        if ($clientIds !== [] && ! in_array($claims['aud'], $clientIds, true)) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google ID token audience is not allowed.'],
            ]);
        }

        if (! filter_var($claims['email'], FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google ID token email is invalid.'],
            ]);
        }

        if (! $this->hasVerifiedEmail($claims['email_verified'] ?? false)) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google account email is not verified.'],
            ]);
        }

        return [
            'sub' => (string) $claims['sub'],
            'email' => (string) $claims['email'],
            'name' => (string) ($claims['name'] ?? $claims['email']),
            'picture' => isset($claims['picture']) ? (string) $claims['picture'] : null,
        ];
    }

    private function hasVerifiedEmail(mixed $value): bool
    {
        return $value === true || $value === 'true' || $value === 1 || $value === '1';
    }
}
