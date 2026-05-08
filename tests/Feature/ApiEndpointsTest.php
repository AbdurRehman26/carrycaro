<?php

use App\Models\City;
use App\Models\Country;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

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

it('logs out the authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/auth/logout');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');
});

it('returns searchable cities and countries', function () {
    $germany = Country::create(['name' => 'Germany', 'code' => 'DE']);
    $uae = Country::create(['name' => 'United Arab Emirates', 'code' => 'AE']);

    City::create(['name' => 'Berlin', 'country_id' => $germany->id, 'city_type' => 'admin']);
    City::create(['name' => 'Dubai', 'country_id' => $uae->id, 'city_type' => 'admin']);

    $citiesResponse = $this->getJson('/api/cities?search=Ber');
    $citiesResponse
        ->assertOk()
        ->assertJsonCount(1, 'cities')
        ->assertJsonPath('cities.0.name', 'Berlin')
        ->assertJsonPath('cities.0.country.name', 'Germany');

    $countriesResponse = $this->getJson('/api/countries?search=United');
    $countriesResponse
        ->assertOk()
        ->assertJsonCount(1, 'countries')
        ->assertJsonPath('countries.0.name', 'United Arab Emirates');
});

it('returns the authenticated profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/profile');

    $response
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.email', $user->email);
});

it('creates and lists trips for the authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $countryA = Country::create(['name' => 'United Kingdom', 'code' => 'GB']);
    $countryB = Country::create(['name' => 'France', 'code' => 'FR']);
    $fromCity = City::create(['name' => 'London', 'country_id' => $countryA->id, 'city_type' => 'admin']);
    $toCity = City::create(['name' => 'Paris', 'country_id' => $countryB->id, 'city_type' => 'admin']);

    $storeResponse = $this->postJson('/api/trips', [
        'from_city_id' => $fromCity->id,
        'to_city_id' => $toCity->id,
        'departure_date' => now()->addDay()->toDateTimeString(),
        'arrival_date' => now()->addDays(2)->toDateTimeString(),
        'weight_available' => 12.5,
        'weight_price' => '45',
        'airline' => 'Air France',
        'notes' => 'Carry-on space available',
    ]);

    $storeResponse
        ->assertCreated()
        ->assertJsonPath('message', 'Trip created successfully.')
        ->assertJsonPath('trip.from_city.name', 'London')
        ->assertJsonPath('trip.to_city.name', 'Paris');

    $tripId = $storeResponse->json('trip.id');

    $myTripsResponse = $this->getJson('/api/my-trips');
    $myTripsResponse
        ->assertOk()
        ->assertJsonPath('data.0.id', $tripId);

    $publicTripsResponse = $this->getJson('/api/trips');
    $publicTripsResponse
        ->assertOk()
        ->assertJsonPath('data.0.id', $tripId);

    expect(Trip::count())->toBe(1);
});
