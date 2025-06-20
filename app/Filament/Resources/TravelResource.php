<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelResource\Pages;
use App\Filament\Resources\TravelResource\RelationManagers\TravelCarryRequestRelationManager;
use App\Models\Travel;
use Filament\Resources\Resource;

class TravelResource extends Resource
{
    protected static ?string $model = Travel::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravel::route('/'),
            'view' => Pages\ViewTravel::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            TravelCarryRequestRelationManager::class
        ];
    }
}
