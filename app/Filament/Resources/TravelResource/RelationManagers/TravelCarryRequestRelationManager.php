<?php

namespace App\Filament\Resources\TravelResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TravelCarryRequestRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $title = 'Carry Requests';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('deleted_at'))
            ->recordTitleAttribute('reviewId')
            ->columns([
                TextColumn::make('CarryRequest.fromCity.name')->formatStateUsing(fn(Model $model) => $model->CarryRequest->fromCity->name . ' (' . $model->CarryRequest->fromCity->country->name . ')')->label('From'),
                TextColumn::make('CarryRequest.toCity.name')->formatStateUsing(fn(Model $model) => $model->CarryRequest->toCity->name . ' (' . $model->CarryRequest->toCity->country->name . ')')->label('To'),
                TextColumn::make('CarryRequest.preferred_date'),
                TextColumn::make('CarryRequest.delivery_deadline'),
                TextColumn::make('user.name'),
                TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'pending' => 'warning',
                    'active' => 'success',
                    'banned' => 'danger',
                    default => 'gray',
                }),
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
                    ->visible(fn($record) => $record->travel->user_id == auth()->id() && $record->user_id != auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->approve())
                    ->icon('heroicon-o-check-circle')
                    ->color('primary'),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn($record) => $record->travel->user_id == auth()->id() && $record->user_id != auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->rejected())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
