<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarryRequestResource\Pages;
use App\Models\CarryRequest;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class CarryRequestResource extends Resource
{
    protected static ?string $model = CarryRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $label = 'Carry Requests';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('myOffer');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarryRequests::route('/'),
            'view' => Pages\ViewCarryRequest::route('/{record}'),
        ];
    }
}
