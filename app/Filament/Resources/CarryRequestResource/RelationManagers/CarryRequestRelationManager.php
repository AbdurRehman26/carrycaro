<?php

namespace App\Filament\Resources\CarryRequestResource\RelationManagers;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CarryRequestRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $label = 'Offer';

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
                TextColumn::make('travel.notes')->limit(20)->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();

                    if (strlen($state[0]) <= $column->getCharacterLimit()) {
                        return null;
                    }

                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state[0];
                })->label('Note'),
                TextColumn::make('travel.user.name'),
                TextColumn::make('status')->badge(),
            ])->actions([
                Action::make('delete')
                    ->label('Delete')
                    ->visible(fn($record) => $record->travel->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->delete())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
                Action::make('approve')
                    ->label('Approve')
                    ->visible(fn($record) => $record->carryRequest->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->approve())
                    ->icon('heroicon-o-check-circle')
                    ->color('primary'),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn($record) => $record->carryRequest->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->rejected())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
