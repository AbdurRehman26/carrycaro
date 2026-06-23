<?php

use App\Models\Airline;
use App\Models\City;
use App\Models\Country;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function createTripFixture(?User $user = null, array $attributes = []): array
{
    $user ??= User::factory()->create();

    $fromCountry = Country::create(['name' => 'Germany', 'code' => 'DE']);
    $toCountry = Country::create(['name' => 'United Arab Emirates', 'code' => 'AE']);
    $fromCity = City::create(['name' => 'Berlin', 'country_id' => $fromCountry->id, 'city_type' => 'admin']);
    $toCity = City::create(['name' => 'Dubai', 'country_id' => $toCountry->id, 'city_type' => 'admin']);

    $trip = Trip::create(array_merge([
        'user_id' => $user->id,
        'from_city_id' => $fromCity->id,
        'to_city_id' => $toCity->id,
        'departure_date' => now()->addDays(5),
        'arrival_date' => now()->addDays(6),
        'weight_available' => 5,
        'weight_price' => '25',
        'airline' => 'Lufthansa',
        'notes' => 'Flexible handoff',
    ], $attributes));

    return [$user, $fromCity, $toCity, $trip];
}

it('requires authentication for protected trip endpoints', function (string $method, string $uri) {
    $this->json($method, $uri)
        ->assertUnauthorized();
})->with([
    ['GET', '/api/my-trips'],
    ['POST', '/api/trips'],
    ['PUT', '/api/trips/1'],
    ['DELETE', '/api/trips/1'],
]);

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

it('resolves an existing airline by name when creating a trip', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $airline = Airline::create(['name' => 'Emirates', 'iata_code' => 'EK', 'icao_code' => 'UAE', 'country' => 'United Arab Emirates']);

    $countryA = Country::create(['name' => 'United Kingdom', 'code' => 'GB']);
    $countryB = Country::create(['name' => 'France', 'code' => 'FR']);
    $fromCity = City::create(['name' => 'London', 'country_id' => $countryA->id, 'city_type' => 'admin']);
    $toCity = City::create(['name' => 'Paris', 'country_id' => $countryB->id, 'city_type' => 'admin']);

    $response = $this->postJson('/api/trips', [
        'from_city_id' => $fromCity->id,
        'to_city_id' => $toCity->id,
        'departure_date' => now()->addDay()->toDateTimeString(),
        'arrival_date' => now()->addDays(2)->toDateTimeString(),
        'weight_available' => 12.5,
        'weight_price' => '45',
        'airline' => 'emirates',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('trip.airline_id', $airline->id)
        ->assertJsonPath('trip.airline', 'Emirates')
        ->assertJsonPath('trip.airline_details.name', 'Emirates');

    expect(Airline::count())->toBe(1);
});

it('creates a missing airline by name when creating a trip', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $countryA = Country::create(['name' => 'United Kingdom', 'code' => 'GB']);
    $countryB = Country::create(['name' => 'France', 'code' => 'FR']);
    $fromCity = City::create(['name' => 'London', 'country_id' => $countryA->id, 'city_type' => 'admin']);
    $toCity = City::create(['name' => 'Paris', 'country_id' => $countryB->id, 'city_type' => 'admin']);

    $response = $this->postJson('/api/trips', [
        'from_city_id' => $fromCity->id,
        'to_city_id' => $toCity->id,
        'departure_date' => now()->addDay()->toDateTimeString(),
        'arrival_date' => now()->addDays(2)->toDateTimeString(),
        'weight_available' => 12.5,
        'weight_price' => '45',
        'airline' => 'CarryCaro Air',
    ]);

    $airline = Airline::where('name', 'CarryCaro Air')->first();

    $response
        ->assertCreated()
        ->assertJsonPath('trip.airline_id', $airline->id)
        ->assertJsonPath('trip.airline', 'CarryCaro Air');

    expect($airline)->not->toBeNull();
});

it('uses an airline id when creating a trip', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $airline = Airline::create(['name' => 'Qatar Airways', 'iata_code' => 'QR', 'icao_code' => 'QTR', 'country' => 'Qatar']);

    $countryA = Country::create(['name' => 'United Kingdom', 'code' => 'GB']);
    $countryB = Country::create(['name' => 'France', 'code' => 'FR']);
    $fromCity = City::create(['name' => 'London', 'country_id' => $countryA->id, 'city_type' => 'admin']);
    $toCity = City::create(['name' => 'Paris', 'country_id' => $countryB->id, 'city_type' => 'admin']);

    $this->postJson('/api/trips', [
        'from_city_id' => $fromCity->id,
        'to_city_id' => $toCity->id,
        'departure_date' => now()->addDay()->toDateTimeString(),
        'arrival_date' => now()->addDays(2)->toDateTimeString(),
        'weight_available' => 12.5,
        'weight_price' => '45',
        'airline_id' => $airline->id,
        'airline' => 'Ignored Client Name',
    ])
        ->assertCreated()
        ->assertJsonPath('trip.airline_id', $airline->id)
        ->assertJsonPath('trip.airline', 'Qatar Airways');
});

it('shows a public trip by id', function () {
    [, , , $trip] = createTripFixture();

    $this->getJson('/api/trips/'.$trip->id)
        ->assertOk()
        ->assertJsonPath('trip.id', $trip->id)
        ->assertJsonPath('trip.from_city.name', 'Berlin')
        ->assertJsonPath('trip.to_city.name', 'Dubai')
        ->assertJsonPath('trip.user.id', $trip->user_id);
});

it('filters public trips by city date and past inclusion', function () {
    [, $fromCity, $toCity, $futureTrip] = createTripFixture(attributes: [
        'departure_date' => now()->addDays(10),
        'arrival_date' => now()->addDays(11),
    ]);

    createTripFixture(attributes: [
        'departure_date' => now()->subDays(10),
        'arrival_date' => now()->subDays(9),
    ]);

    $this->getJson('/api/trips?from_city_id='.$fromCity->id.'&to_city_id='.$toCity->id.'&departure_from='.now()->addDays(9)->toDateString().'&departure_to='.now()->addDays(12)->toDateString())
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $futureTrip->id);

    $this->getJson('/api/trips?include_past=1')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('validates trip creation', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/trips', [
        'from_city_id' => 999,
        'to_city_id' => 999,
        'departure_date' => now()->subDay()->toDateString(),
        'arrival_date' => now()->subDays(2)->toDateString(),
        'weight_available' => 0,
        'weight_price' => '',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'from_city_id',
            'to_city_id',
            'departure_date',
            'arrival_date',
            'weight_available',
            'weight_price',
        ]);
});

it('updates an owned trip', function () {
    [$user, , , $trip] = createTripFixture();
    Sanctum::actingAs($user);

    $this->putJson('/api/trips/'.$trip->id, [
        'weight_available' => 9,
        'weight_price' => '35',
        'airline' => 'Emirates',
        'notes' => 'Updated availability',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Trip updated successfully.')
        ->assertJsonPath('trip.weight_available', 9)
        ->assertJsonPath('trip.airline', 'Emirates');

    expect($trip->fresh()->weight_price)->toBe('35');
});

it('prevents updating another users trip', function () {
    [, , , $trip] = createTripFixture();
    Sanctum::actingAs(User::factory()->create());

    $this->putJson('/api/trips/'.$trip->id, [
        'weight_price' => '100',
    ])
        ->assertForbidden()
        ->assertJsonPath('message', 'Unauthorized.');

    expect($trip->fresh()->weight_price)->toBe('25');
});

it('validates trip updates', function () {
    [$user, , , $trip] = createTripFixture();
    Sanctum::actingAs($user);

    $this->putJson('/api/trips/'.$trip->id, [
        'from_city_id' => 999,
        'to_city_id' => 999,
        'departure_date' => 'not-a-date',
        'arrival_date' => 'not-a-date',
        'weight_available' => 0,
        'weight_price' => str_repeat('1', 256),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'from_city_id',
            'to_city_id',
            'departure_date',
            'arrival_date',
            'weight_available',
            'weight_price',
        ]);
});

it('deletes an owned trip', function () {
    [$user, , , $trip] = createTripFixture();
    Sanctum::actingAs($user);

    $this->deleteJson('/api/trips/'.$trip->id)
        ->assertOk()
        ->assertJsonPath('message', 'Trip deleted successfully.');

    $this->assertSoftDeleted('trips', ['id' => $trip->id]);
});

it('prevents deleting another users trip', function () {
    [, , , $trip] = createTripFixture();
    Sanctum::actingAs(User::factory()->create());

    $this->deleteJson('/api/trips/'.$trip->id)
        ->assertForbidden()
        ->assertJsonPath('message', 'Unauthorized.');

    expect($trip->fresh()->deleted_at)->toBeNull();
});

it('returns not found for missing trips', function () {
    $this->getJson('/api/trips/999')->assertNotFound();

    Sanctum::actingAs(User::factory()->create());

    $this->putJson('/api/trips/999', ['weight_price' => '25'])->assertNotFound();
    $this->deleteJson('/api/trips/999')->assertNotFound();
});
