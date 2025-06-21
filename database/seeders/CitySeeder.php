<?php

namespace Database\Seeders;

use App\Imports\CitiesImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        Excel::import(new CitiesImport(), 'public/worldcities.csv');
    }
}
