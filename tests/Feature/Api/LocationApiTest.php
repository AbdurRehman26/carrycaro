<?php

use App\Models\City;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

it('filters cities by q alias and country id', function () {
    $germany = Country::create(['name' => 'Germany', 'code' => 'DE']);
    $france = Country::create(['name' => 'France', 'code' => 'FR']);

    City::create(['name' => 'Berlin', 'country_id' => $germany->id, 'city_type' => 'admin']);
    City::create(['name' => 'Bernau', 'country_id' => $germany->id, 'city_type' => 'city']);
    City::create(['name' => 'Bordeaux', 'country_id' => $france->id, 'city_type' => 'city']);

    $this->getJson('/api/cities?q=Ber&country_id='.$germany->id)
        ->assertOk()
        ->assertJsonCount(2, 'cities')
        ->assertJsonPath('cities.0.country.code', 'DE');
});

it('filters countries by q alias', function () {
    Country::create(['name' => 'United Kingdom', 'code' => 'GB']);
    Country::create(['name' => 'United Arab Emirates', 'code' => 'AE']);
    Country::create(['name' => 'Germany', 'code' => 'DE']);

    $this->getJson('/api/countries?q=United')
        ->assertOk()
        ->assertJsonCount(2, 'countries');
});
