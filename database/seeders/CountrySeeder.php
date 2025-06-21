<?php

namespace Database\Seeders;

use App\Imports\CountriesImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Excel::import(new CountriesImport(), 'public/worldcities.csv');
   }
}
