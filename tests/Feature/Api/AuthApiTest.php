<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    $response = $this->postJson('/api/auth/social-login', [
        'provider' => 'google',
        'provider_id' => 'google-user-123',
        'name' => 'Jamie Doe',
        'email' => 'jamie@example.com',
        'profile_image' => 'https://example.com/avatar.jpg',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'profile_image'],
            'token',
        ]);

    expect(User::where('email', 'jamie@example.com')->exists())->toBeTrue();
});

it('updates an existing user during social login', function () {
    $user = User::factory()->create([
        'email' => 'jamie@example.com',
        'profile_image' => 'https://example.com/old.jpg',
    ]);

    $this->postJson('/api/auth/social-login', [
        'provider' => 'google',
        'provider_id' => 'google-user-456',
        'name' => 'Jamie Doe',
        'email' => 'jamie@example.com',
        'profile_image' => 'https://example.com/new.jpg',
    ])
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.profile_image', 'https://example.com/new.jpg');

    expect($user->fresh()->google_id)->toBe('google-user-456');
});

it('validates social login payloads', function () {
    $this->postJson('/api/auth/social-login', [
        'provider' => 'facebook',
        'provider_id' => '',
        'name' => '',
        'email' => 'not-an-email',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['provider', 'provider_id', 'name', 'email']);
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
