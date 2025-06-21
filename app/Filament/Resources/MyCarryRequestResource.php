<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarryRequestResource\RelationManagers\CarryRequestRelationManager;
use App\Filament\Resources\MyCarryRequestResource\Pages;
use App\Models\CarryRequest;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MyCarryRequestResource extends Resource
{
    protected static ?string $model = CarryRequest::class;

    protected static ?string $navigationGroup = 'Your Requests';

    protected static ?string $navigationLabel = 'My Carry Requests';

    protected static ?string $label = 'My Carry Requests';

    protected static ?string $navigationIcon = 'fas-truck-fast';

    public static function table(Table $table): Table
    {
        return $table
            ->query(CarryRequest::with('offers')->where('user_id', auth()->user()->id))
            ->columns([
                TextColumn::make('fromCity.name')->formatStateUsing(fn(Model $model) => $model->fromCity->name . ' (' . $model->fromCity->country->name . ')')->label('From'),
                TextColumn::make('toCity.name')->formatStateUsing(fn(Model $model) => $model->toCity->name . ' (' . $model->toCity->country->name . ')')->label('To'),
                TextColumn::make('preferred_date')->label('Delivery Date')->date(),
                TextColumn::make('delivery_deadline')->label('Delivery Deadline')->date(),
                TextColumn::make('weight')->suffix(' Kg'),
                TextColumn::make('price'),
                TextColumn::make('id')->label('To buy')->badge()->formatStateUsing(fn(Model $model) => $model->products()->count() ? 'Yes' : 'No'),
                TextColumn::make('offers_count')
                    ->label('Delivery Offers')
                    ->counts('offers')
                    ->badge(),
            ])->recordUrl(fn(CarryRequest $record) => CarryRequestResource::getUrl('view', [
                'record' => $record->id,
            ]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyCarryRequests::route('/')
        ];
    }

    public static function getRelations(): array
    {
        return [
            CarryRequestRelationManager::class
        ];
    }
}
