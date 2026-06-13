<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns the authenticated profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/profile');

    $response
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.email', $user->email);
});

it('requires authentication for profile endpoints', function (string $method) {
    $this->json($method, '/api/profile')
        ->assertUnauthorized();
})->with(['GET', 'PUT', 'DELETE']);

it('updates the authenticated profile', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone_number' => null,
    ]);
    Sanctum::actingAs($user);

    $this->putJson('/api/profile', [
        'name' => 'New Name',
        'phone_number' => '+491234567890',
        'facebook_profile' => 'https://facebook.com/new-name',
        'timezone' => 'Europe/Berlin',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Profile updated successfully.')
        ->assertJsonPath('user.name', 'New Name')
        ->assertJsonPath('user.timezone', 'Europe/Berlin');

    $user->refresh();

    expect($user->phone_number)->toBe('+491234567890')
        ->and(Hash::check('new-password', $user->password))->toBeTrue();
});

it('validates profile updates', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->putJson('/api/profile', [
        'name' => str_repeat('a', 256),
        'phone_number' => str_repeat('1', 21),
        'password' => 'short',
        'password_confirmation' => 'different',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'phone_number', 'password']);
});

it('deletes the authenticated profile and revokes tokens', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $this->withToken($token)
        ->deleteJson('/api/profile')
        ->assertOk()
        ->assertJsonPath('message', 'Account deleted successfully.');

    $this->assertSoftDeleted('users', ['id' => $user->id]);
    expect($user->tokens()->count())->toBe(0);
});
