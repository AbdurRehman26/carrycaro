<?php

use App\Models\Airline;
use Database\Seeders\AirlineSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists active airlines ordered by name', function () {
    Airline::create(['name' => 'Zulu Air', 'iata_code' => 'ZU', 'icao_code' => 'ZUL', 'country' => 'Testland']);
    Airline::create(['name' => 'Alpha Air', 'iata_code' => 'AA', 'icao_code' => 'AAA', 'country' => 'Testland']);
    Airline::create(['name' => 'Inactive Air', 'iata_code' => 'IA', 'icao_code' => 'INA', 'country' => 'Testland', 'is_active' => false]);

    $this->getJson('/api/airlines')
        ->assertOk()
        ->assertJsonCount(2, 'airlines')
        ->assertJsonPath('airlines.0.name', 'Alpha Air')
        ->assertJsonPath('airlines.1.name', 'Zulu Air');
});

it('searches airlines by name code and country', function () {
    Airline::create(['name' => 'Emirates', 'iata_code' => 'EK', 'icao_code' => 'UAE', 'country' => 'United Arab Emirates']);
    Airline::create(['name' => 'Lufthansa', 'iata_code' => 'LH', 'icao_code' => 'DLH', 'country' => 'Germany']);
    Airline::create(['name' => 'Qatar Airways', 'iata_code' => 'QR', 'icao_code' => 'QTR', 'country' => 'Qatar']);

    $this->getJson('/api/airlines?q=UAE')
        ->assertOk()
        ->assertJsonCount(1, 'airlines')
        ->assertJsonPath('airlines.0.name', 'Emirates');

    $this->getJson('/api/airlines?search=Germany')
        ->assertOk()
        ->assertJsonCount(1, 'airlines')
        ->assertJsonPath('airlines.0.name', 'Lufthansa');
});

it('seeds famous airlines', function () {
    $this->seed(AirlineSeeder::class);

    expect(Airline::where('name', 'Emirates')->exists())->toBeTrue()
        ->and(Airline::where('name', 'Lufthansa')->exists())->toBeTrue()
        ->and(Airline::where('name', 'Qatar Airways')->exists())->toBeTrue()
        ->and(Airline::count())->toBeGreaterThanOrEqual(25);
});
