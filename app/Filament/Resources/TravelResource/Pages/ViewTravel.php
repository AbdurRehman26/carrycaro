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
                Section::make('Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('fromCity.country.name')->color('primary')->label('From Country'),
                            TextEntry::make('toCity.country.name')->color('primary')->label('To Country'),
                            TextEntry::make('fromCity.name')->color('primary')->label('From City'),
                            TextEntry::make('toCity.name')->color('primary')->label('To City'),
                            TextEntry::make('departure_date')->color('primary')->date(),
                            TextEntry::make('arrival_date')->color('primary')->date(),
                            TextEntry::make('weight_available')->color('primary')->suffix(' Kg'),
                            TextEntry::make('weight_price')->color('primary'),
                            TextEntry::make('updated_at')->label('Airline')->color('primary')->formatStateUsing(fn ($state, $record) => blank($record->notes) ? 'N/A' : $record->notes),
                            TextEntry::make('id')->label('Notes')->color('primary')->formatStateUsing(fn ($state, $record) => blank($record->notes) ? 'N/A' : $record->notes),
                            TextEntry::make('created_at')->color('primary')->label('Created')->since(),
                            TextEntry::make('user.name')->color('primary')->label('User'),
                        ]),
                    ]),
            ]);
    }
}
