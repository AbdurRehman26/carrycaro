<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryRequestResource\Pages;
use App\Models\DeliveryRequest;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class DeliveryRequestResource extends Resource
{
    protected static ?string $model = DeliveryRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $label = 'Carry Requests';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('myMatch');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryRequests::route('/'),
            'view' => Pages\ViewDeliveryRequest::route('/{record}'),
        ];
    }
}
