<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripResource\Pages;
use App\Filament\Resources\TripResource\RelationManagers\TripCarryRequestRelationManager;
use App\Models\Trip;
use Filament\Resources\Resource;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrip::route('/'),
            'view' => Pages\ViewTrip::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            TripCarryRequestRelationManager::class
        ];
    }
}
