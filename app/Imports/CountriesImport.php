<?php

namespace App\Imports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class CountriesImport implements WithBatchInserts, WithSkipDuplicates, WithHeadingRow, ToModel, WithChunkReading
{
    public function model(array $row): Country
    {
        $now = now();

        return new Country([
            'name' => $row['country'],
            'code' => $row['iso3'],
            'updated_at' => $now,
            'created_at' => $now,
        ]);
    }

    public function chunkSize(): int
    {
        return 5000;
    }

    public function batchSize(): int
    {
        return 5000;
    }
}
