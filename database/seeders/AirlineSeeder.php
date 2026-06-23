<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [
            ['name' => 'Lufthansa', 'iata_code' => 'LH', 'icao_code' => 'DLH', 'country' => 'Germany'],
            ['name' => 'Emirates', 'iata_code' => 'EK', 'icao_code' => 'UAE', 'country' => 'United Arab Emirates'],
            ['name' => 'Qatar Airways', 'iata_code' => 'QR', 'icao_code' => 'QTR', 'country' => 'Qatar'],
            ['name' => 'Turkish Airlines', 'iata_code' => 'TK', 'icao_code' => 'THY', 'country' => 'Turkey'],
            ['name' => 'Air France', 'iata_code' => 'AF', 'icao_code' => 'AFR', 'country' => 'France'],
            ['name' => 'British Airways', 'iata_code' => 'BA', 'icao_code' => 'BAW', 'country' => 'United Kingdom'],
            ['name' => 'KLM Royal Dutch Airlines', 'iata_code' => 'KL', 'icao_code' => 'KLM', 'country' => 'Netherlands'],
            ['name' => 'Singapore Airlines', 'iata_code' => 'SQ', 'icao_code' => 'SIA', 'country' => 'Singapore'],
            ['name' => 'Cathay Pacific', 'iata_code' => 'CX', 'icao_code' => 'CPA', 'country' => 'Hong Kong'],
            ['name' => 'Etihad Airways', 'iata_code' => 'EY', 'icao_code' => 'ETD', 'country' => 'United Arab Emirates'],
            ['name' => 'Swiss International Air Lines', 'iata_code' => 'LX', 'icao_code' => 'SWR', 'country' => 'Switzerland'],
            ['name' => 'Austrian Airlines', 'iata_code' => 'OS', 'icao_code' => 'AUA', 'country' => 'Austria'],
            ['name' => 'United Airlines', 'iata_code' => 'UA', 'icao_code' => 'UAL', 'country' => 'United States'],
            ['name' => 'Delta Air Lines', 'iata_code' => 'DL', 'icao_code' => 'DAL', 'country' => 'United States'],
            ['name' => 'American Airlines', 'iata_code' => 'AA', 'icao_code' => 'AAL', 'country' => 'United States'],
            ['name' => 'Air Canada', 'iata_code' => 'AC', 'icao_code' => 'ACA', 'country' => 'Canada'],
            ['name' => 'Qantas', 'iata_code' => 'QF', 'icao_code' => 'QFA', 'country' => 'Australia'],
            ['name' => 'Japan Airlines', 'iata_code' => 'JL', 'icao_code' => 'JAL', 'country' => 'Japan'],
            ['name' => 'All Nippon Airways', 'iata_code' => 'NH', 'icao_code' => 'ANA', 'country' => 'Japan'],
            ['name' => 'Korean Air', 'iata_code' => 'KE', 'icao_code' => 'KAL', 'country' => 'South Korea'],
            ['name' => 'Thai Airways', 'iata_code' => 'TG', 'icao_code' => 'THA', 'country' => 'Thailand'],
            ['name' => 'Malaysia Airlines', 'iata_code' => 'MH', 'icao_code' => 'MAS', 'country' => 'Malaysia'],
            ['name' => 'IndiGo', 'iata_code' => '6E', 'icao_code' => 'IGO', 'country' => 'India'],
            ['name' => 'Air India', 'iata_code' => 'AI', 'icao_code' => 'AIC', 'country' => 'India'],
            ['name' => 'Saudia', 'iata_code' => 'SV', 'icao_code' => 'SVA', 'country' => 'Saudi Arabia'],
            ['name' => 'Egyptair', 'iata_code' => 'MS', 'icao_code' => 'MSR', 'country' => 'Egypt'],
            ['name' => 'Ethiopian Airlines', 'iata_code' => 'ET', 'icao_code' => 'ETH', 'country' => 'Ethiopia'],
            ['name' => 'Kenya Airways', 'iata_code' => 'KQ', 'icao_code' => 'KQA', 'country' => 'Kenya'],
            ['name' => 'LATAM Airlines', 'iata_code' => 'LA', 'icao_code' => 'LAN', 'country' => 'Chile'],
            ['name' => 'Iberia', 'iata_code' => 'IB', 'icao_code' => 'IBE', 'country' => 'Spain'],
        ];

        foreach ($airlines as $airline) {
            Airline::query()->updateOrCreate(
                ['name' => $airline['name']],
                $airline + ['is_active' => true]
            );
        }
    }
}
