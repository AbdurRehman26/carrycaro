<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('registers a user and returns an auth token', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Alex Carter',
        'email' => 'alex@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone_number' => '+49123456789',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Registration successful. Please verify your email.')
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'phone_number'],
            'token',
        ]);

    expect(User::where('email', 'alex@example.com')->exists())->toBeTrue();
});

it('validates registration payloads', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/auth/register', [
        'name' => '',
        'email' => 'taken@example.com',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('logs in a user with email and password', function () {
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
});

it('rejects invalid login credentials', function () {
    User::factory()->create([
        'email' => 'alex@example.com',
        'password' => 'password123',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'alex@example.com',
        'password' => 'wrong-password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('handles social login and returns an auth token', function () {
    config(['services.google.client_ids' => ['mobile-client-id.apps.googleusercontent.com']]);

    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'aud' => 'mobile-client-id.apps.googleusercontent.com',
            'sub' => 'google-user-123',
            'name' => 'Jamie Doe',
            'email' => 'jamie@example.com',
            'email_verified' => 'true',
            'picture' => 'https://example.com/avatar.jpg',
        ]),
    ]);

    $response = $this->postJson('/api/auth/social-login', [
        'provider' => 'google',
        'id_token' => 'valid-google-id-token',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'profile_image'],
            'token',
        ]);

    $user = User::where('email', 'jamie@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-user-123')
        ->and($user->profile_image)->toBe('https://example.com/avatar.jpg');
});

it('updates an existing user during social login', function () {
    config(['services.google.client_ids' => ['mobile-client-id.apps.googleusercontent.com']]);

    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'aud' => 'mobile-client-id.apps.googleusercontent.com',
            'sub' => 'google-user-456',
            'name' => 'Jamie Doe',
            'email' => 'jamie@example.com',
            'email_verified' => true,
            'picture' => 'https://example.com/new.jpg',
        ]),
    ]);

    $user = User::factory()->create([
        'email' => 'jamie@example.com',
        'profile_image' => 'https://example.com/old.jpg',
    ]);

    $this->postJson('/api/auth/social-login', [
        'provider' => 'google',
        'id_token' => 'valid-google-id-token',
    ])
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.profile_image', 'https://example.com/new.jpg');

    expect($user->fresh()->google_id)->toBe('google-user-456');
});

it('validates social login payloads', function () {
    $this->postJson('/api/auth/social-login', [
        'provider' => 'facebook',
        'id_token' => '',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['provider', 'id_token']);
});

it('rejects invalid google id tokens', function () {
    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'error' => 'invalid_token',
        ], 400),
    ]);

    $this->postJson('/api/auth/social-login', [
        'provider' => 'google',
        'id_token' => 'invalid-google-id-token',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['id_token']);
});

it('rejects google id tokens from unexpected audiences', function () {
    config(['services.google.client_ids' => ['expected-mobile-client-id.apps.googleusercontent.com']]);

    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'aud' => 'other-client-id.apps.googleusercontent.com',
            'sub' => 'google-user-123',
            'name' => 'Jamie Doe',
            'email' => 'jamie@example.com',
            'email_verified' => true,
        ]),
    ]);

    $this->postJson('/api/auth/social-login', [
        'provider' => 'google',
        'id_token' => 'wrong-audience-google-id-token',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['id_token']);
});

it('logs out the authenticated user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/auth/logout');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');

    expect($user->tokens()->count())->toBe(0);
});

it('requires authentication to log out', function () {
    $this->postJson('/api/auth/logout')
        ->assertUnauthorized();
});
