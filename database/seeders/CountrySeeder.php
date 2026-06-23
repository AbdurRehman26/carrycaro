<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use SplFileObject;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [];

        foreach ($this->rows() as $row) {
            if (empty($row['country'])) {
                continue;
            }

            $countries[$row['country']] = [
                'name' => $row['country'],
                'code' => $row['iso3'] ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk(array_values($countries), 500) as $chunk) {
            Country::query()->insert($chunk);
        }
    }

    /**
     * @return iterable<array<string, string|null>>
     */
    private function rows(): iterable
    {
        $file = new SplFileObject(base_path('public/worldcities.csv'));
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $headers = [];

        foreach ($file as $index => $row) {
            if ($row === [null] || $row === false) {
                continue;
            }

            if ($index === 0) {
                $headers = $row;

                continue;
            }

            yield array_combine($headers, array_pad($row, count($headers), null));
        }
    }
}
