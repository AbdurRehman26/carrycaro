<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\City;
use App\Models\Country;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();
        Schema::disableForeignKeyConstraints();

        try {
            DB::table('notifications')->truncate();
            ChatMessage::query()->truncate();
            ChatConversation::query()->truncate();
            Trip::query()->truncate();
            User::query()->truncate();
            Airline::query()->truncate();
            City::query()->truncate();
            Country::query()->truncate();

            $this->call([
                UserSeeder::class,
                CountrySeeder::class,
                CitySeeder::class,
                AirlineSeeder::class,
                TripSeeder::class,
                ChatSeeder::class,
                NotificationSeeder::class,
            ]);
        } finally {
            Schema::enableForeignKeyConstraints();
            DB::enableQueryLog();
        }
    }
}
