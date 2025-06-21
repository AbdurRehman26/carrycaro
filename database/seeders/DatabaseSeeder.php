<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::query()->truncate();
        Country::query()->truncate();
        City::query()->truncate();

        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
        ]);

        User::factory()->count(20)->create();

        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::enableQueryLog();
    }
}
