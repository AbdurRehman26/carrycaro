<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyTravelResource\Pages;
use App\Filament\Resources\TravelResource\Pages\ViewTravel;
use App\Models\Travel;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyTravelResource extends Resource
{
    protected static ?string $model = Travel::class;

    protected static ?string $navigationGroup = 'Your Requests';

    protected static ?string $label = 'My Travel';

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->user()->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fromCity.name')
                    ->label('From City'),
                Tables\Columns\TextColumn::make('toCity.name')
                    ->label('To City'),
                Tables\Columns\TextColumn::make('fromCity.country.name')
                    ->label('From Country'),
                Tables\Columns\TextColumn::make('toCity.country.name')
                    ->label('To Country'),
                Tables\Columns\TextColumn::make('departure_date')
                    ->date()
                    ->label('Departure Date'),
                Tables\Columns\TextColumn::make('arrival_date')
                    ->date()
                    ->label('Arrival Date'),
                Tables\Columns\TextColumn::make('airline')
                    ->label('Airline'),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->label('Notes'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyTravel::route('/'),
            'view' => ViewTravel::route('/{record}'),
        ];
    }
}
