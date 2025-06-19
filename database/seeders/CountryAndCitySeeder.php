<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CountryAndCitySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Country::query()->truncate();
        City::query()->truncate();

        $now = now();
        $collection = Excel::toCollection(null, 'public/worldcities.csv')->first();

        $countries = [];
        $cities = [];

        foreach ($collection as $key => $data) {
            if(!$key){
                continue;
            }

            if(! in_array($data[4], array_column($countries, 'name'))){
                $countries[] = [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'name' => $data[4],
                    'code' => $data[6]
                ];
            }
        }

        Country::query()->insert($countries);
        $dbCountries = Country::query()->pluck('id', 'name')->toArray();

        foreach ($collection as $key => $data) {
            if(!$key){
                continue;
            }

            if(! in_array($data[1], array_column($cities, 'name'))){
                $cities[] = [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'name' => $data[1] ?? $data[0],
                    'city_type' => $data[8],
                    'country_id' => $dbCountries[$data[4]]
                ];
            }

            if($key % 500 === 0){
                City::query()->insert($cities);
                $cities = [];
            }
        }
   }
}
