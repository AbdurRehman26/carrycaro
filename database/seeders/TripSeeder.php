<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();
        $cities = City::query()->inRandomOrder()->limit(20)->get();
        $airlines = Airline::query()->orderBy('name')->get();

        if ($users->isEmpty() || $cities->count() < 2) {
            return;
        }

        if ($airlines->isEmpty()) {
            $airlines = collect(['Lufthansa', 'Emirates', 'Qatar Airways', 'Turkish Airlines', 'Air France', 'British Airways'])
                ->map(fn (string $name) => Airline::query()->create(['name' => $name, 'is_active' => true]));
        }

        foreach ($users->take(15) as $index => $user) {
            $fromCity = $cities[$index % $cities->count()];
            $toCity = $cities[($index + 1) % $cities->count()];
            $airline = $airlines[$index % $airlines->count()];
            $departureDate = now()->addDays($index + 2);

            Trip::query()->create([
                'user_id' => $user->id,
                'from_city_id' => $fromCity->id,
                'to_city_id' => $toCity->id,
                'departure_date' => $departureDate,
                'arrival_date' => $departureDate->copy()->addDay(),
                'airline_id' => $airline->id,
                'airline' => $airline->name,
                'notes' => 'Seeded trip with available luggage space.',
                'weight_available' => fake()->randomFloat(1, 2, 25),
                'weight_price' => (string) fake()->numberBetween(10, 80),
            ]);
        }
    }
}
