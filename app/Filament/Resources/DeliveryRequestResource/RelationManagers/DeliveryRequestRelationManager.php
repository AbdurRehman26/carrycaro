<?php

namespace App\Filament\Resources\DeliveryRequestResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryRequestRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    protected static ?string $label = 'Match';

    protected static ?string $title = 'Delivery Offers';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('deleted_at'))
            ->recordTitleAttribute('reviewId')
            ->columns([
                TextColumn::make('travel.fromCity.name')->formatStateUsing(fn(Model $model) => $model->travel->fromCity->name . ' (' . $model->travel->fromCity->country->name . ')')->label('From'),
                TextColumn::make('travel.toCity.name')->formatStateUsing(fn(Model $model) => $model->travel->toCity->name . ' (' . $model->travel->toCity->country->name . ')')->label('To'),
                TextColumn::make('travel.departure_date')->date()->label('Departure Date'),
                TextColumn::make('travel.arrival_date')->date()->label('Arrival Date'),
                TextColumn::make('travel.airline')->label('Airline'),
                TextColumn::make('travel.notes')->label('Note'),
                TextColumn::make('user.name'),
                TextColumn::make('status')->badge(),
            ])->actions([
                Action::make('delete')
                    ->label('Delete')
                    ->visible(fn($record) => $record->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->delete())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
                Action::make('approve')
                    ->label('Approve')
                    ->visible(fn($record) => $record->deliveryRequest->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->approve())
                    ->icon('heroicon-o-check-circle')
                    ->color('primary'),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn($record) => $record->deliveryRequest->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->rejected())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
