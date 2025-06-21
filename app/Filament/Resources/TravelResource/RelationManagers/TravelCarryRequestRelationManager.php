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
                TextColumn::make('carryRequest.fromCity.name')->formatStateUsing(fn(Model $model) => $model->CarryRequest->fromCity->name . ' (' . $model->CarryRequest->fromCity->country->name . ')')->label('From'),
                TextColumn::make('carryRequest.toCity.name')->formatStateUsing(fn(Model $model) => $model->CarryRequest->toCity->name . ' (' . $model->CarryRequest->toCity->country->name . ')')->label('To'),
                TextColumn::make('carryRequest.preferred_date'),
                TextColumn::make('carryRequest.delivery_deadline'),
                TextColumn::make('carryRequest.user.name'),
                TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'banned' => 'danger',
                    default => 'gray',
                }),
            ])->actions([
                Action::make('delete')
                    ->label('Delete')
                    ->visible(fn($record) => $record->carryRequest->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->delete())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
                Action::make('approve')
                    ->label('Approve')
                    ->visible(fn($record) => $record->status == 'pending' && $record->travel->user_id == auth()->id() && $record->user_id != auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->approve())
                    ->icon('heroicon-o-check-circle')
                    ->color('primary'),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn($record) => $record->status == 'pending' && $record->travel->user_id == auth()->id() && $record->user_id != auth()->id())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->reject())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
