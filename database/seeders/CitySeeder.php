<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use SplFileObject;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $countryIds = Country::query()->pluck('id', 'name');
        $cities = [];

        foreach ($this->rows() as $row) {
            $countryId = $countryIds[$row['country'] ?? ''] ?? null;

            if (! $countryId || empty($row['city'])) {
                continue;
            }

            $cities[] = [
                'name' => $row['city_ascii'] ?: $row['city'],
                'country_id' => $countryId,
                'city_type' => $row['capital'] ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($cities) === 1000) {
                City::query()->insert($cities);
                $cities = [];
            }
        }

        if ($cities !== []) {
            City::query()->insert($cities);
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
