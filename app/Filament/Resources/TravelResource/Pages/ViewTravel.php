<?php

namespace App\Filament\Resources\TravelResource\Pages;

use App\Filament\Resources\TravelResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTravel extends ViewRecord
{
    protected static string $resource = TravelResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Delivery Info')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('fromCity.country.name')->label('From Country'),
                            TextEntry::make('toCity.country.name')->label('To Country'),
                            TextEntry::make('fromCity.name')->label('From City'),
                            TextEntry::make('toCity.name')->label('To City'),
                            TextEntry::make('departure_date')->date(),
                            TextEntry::make('arrival_date')->date(),
                            TextEntry::make('weight_available')->suffix(' Kg'),
                            TextEntry::make('weight_price'),
                            TextEntry::make('airline'),
                            TextEntry::make('notes'),
                            TextEntry::make('created_at')->label('Created')->since(),
                            TextEntry::make('user.name')->label('User'),
                        ]),
                    ]),
            ]);
    }
}
