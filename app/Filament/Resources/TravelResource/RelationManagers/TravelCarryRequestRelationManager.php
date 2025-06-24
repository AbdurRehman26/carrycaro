<?php

namespace App\Filament\Resources\TravelResource\RelationManagers;

use App\Enums\GeneralStatus;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class TravelCarryRequestRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $title = 'Carry Offers';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('deleted_at'))
            ->recordTitleAttribute('reviewId')
            ->columns([
                TextColumn::make('carryRequest.fromCity.name')->formatStateUsing(fn(Model $model) => $model->CarryRequest->fromCity->name . ' (' . $model->CarryRequest->fromCity->country->name . ')')->label('From'),
                TextColumn::make('carryRequest.toCity.name')->formatStateUsing(fn(Model $model) => $model->CarryRequest->toCity->name . ' (' . $model->CarryRequest->toCity->country->name . ')')->label('To'),
                TextColumn::make('carryRequest.preferred_date')->label('Preferred Date'),
                TextColumn::make('carryRequest.delivery_deadline')->label('Delivery Deadline'),
                TextColumn::make('carryRequest.user.name'),
                PhoneColumn::make('carryRequest.user.phone_number')
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->formatStateUsing(fn($record, $state) => $record->canSeeEachOtherDetails() ? $state : 'Not Authorized')
                    ->label('Phone Number'),
                TextColumn::make('carryRequest.user.facebook_profile')
                    ->formatStateUsing(fn($record, $state) => $record->canSeeEachOtherDetails() ? 'Link' : 'Not Authorized' )
                    ->url(fn ($record, $state) => $record->canSeeEachOtherDetails() ? $state : '')
                    ->icon('heroicon-o-link'),
                TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    GeneralStatus::PENDING => 'warning',
                    GeneralStatus::APPROVED => 'success',
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
                    ->modalHeading('Once you approve your contact details will be shared with the requester.')
                    ->visible(fn($record) => $record->status == GeneralStatus::PENDING && $record->travel->user_id == auth()->id() && $record->user_id != auth()->id())
                    ->requiresConfirmation()
                    ->action(function($record){
                        Notification::make()
                            ->success()
                            ->title('Carry Offer Accepted')
                            ->sendToDatabase($record->travel->user)
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color('primary'),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn($record) => $record->status == GeneralStatus::PENDING && $record->travel->user_id == auth()->id() && $record->user_id != auth()->id())
                    ->requiresConfirmation()
                    ->action(function($record){
                        $record->reject();
                        Notification::make()
                            ->danger()
                            ->title('Carry Offer Rejected')
                            ->sendToDatabase($record->travel->user)
                            ->send();

                    })
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
