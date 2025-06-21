<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class CitiesImport implements WithBatchInserts, WithSkipDuplicates, WithHeadingRow, ToModel, WithChunkReading
{
    public function chunkSize(): int
    {
        return 500; // adjust based on available memory
    }

    public function batchSize(): int
    {
        return 500; // adjust based on available memory
    }

    public function model(array $row): City
    {
        $country = Country::query()->where('name', $row['country'])->first();
        $now = now();

        return new City([
            'created_at' => $now,
            'updated_at' => $now,
            'name' => $row['city_ascii'] ?? $row['city'],
            'city_type' => $row['capital'],
            'country_id' => $country->id
        ]);
    }
}
