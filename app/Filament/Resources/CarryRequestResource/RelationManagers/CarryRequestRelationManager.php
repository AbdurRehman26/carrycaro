<?php

namespace App\Filament\Resources\CarryRequestResource\RelationManagers;

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
            ->columns(components: [
                TextColumn::make('trip.fromCity.name')->formatStateUsing(fn(Model $model) => $model->trip->fromCity->name . ' (' . $model->trip->fromCity->country->name . ')')->label('From'),
                TextColumn::make('trip.toCity.name')->formatStateUsing(fn(Model $model) => $model->trip->toCity->name . ' (' . $model->trip->toCity->country->name . ')')->label('To'),
                TextColumn::make('trip.departure_date')->date()->label('Departure Date'),
                TextColumn::make('trip.arrival_date')->date()->label('Arrival Date'),
                TextColumn::make('trip.airline')->label('Airline'),
                TextColumn::make('trip.notes')->limit(10)->tooltip(fn($record) => $record->trip->notes)->label('Note'),
                TextColumn::make('trip.user.name'),
                PhoneColumn::make('trip.user.phone_number')
                    ->displayFormat(PhoneInputNumberType::NATIONAL)
                    ->formatStateUsing(fn($record, $state) => $record->canSeeEachOtherDetails() ? $state : 'Not Authorized')
                    ->label('Phone'),
                TextColumn::make('trip.user.facebook_profile')
                    ->formatStateUsing(fn($record, $state) => $record->canSeeEachOtherDetails() ? 'Link' : 'Not Authorized' )
                    ->url(fn ($record, $state) => $record->canSeeEachOtherDetails() ? $state : '')
                    ->icon('heroicon-o-link')
                    ->label('Facebook Url'),
                TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    GeneralStatus::PENDING => 'warning',
                    GeneralStatus::APPROVED => 'success',
                    'banned' => 'danger',
                    default => 'gray',
                }),
            ])->actions([
                Action::make('delete')
                    ->label('Delete')
                    ->visible(fn($record) => $record->trip->user_id == auth()->id())
                    ->requiresConfirmation()
                    ->action(function($record){
                        $record->delete();
                        Notification::make()
                            ->title('Carry Request Deleted')
                            ->send();
                    })
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
                Action::make('approve')
                    ->label('Approve')
                    ->visible(fn($record) => $record->carryRequest->user_id == auth()->id() && $record->status == GeneralStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Once you approve your contact details will be shared with the traveller.')
                    ->action(function($record){
                        $record->approve();
                        Notification::make()
                            ->success()
                            ->title('Carry Request Approved')
                            ->sendToDatabase($record->carryRequest->user)
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color('primary'),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn($record) => $record->carryRequest->user_id == auth()->id() && $record->status == GeneralStatus::PENDING)
                    ->requiresConfirmation()
                    ->action(function($record){
                        $record->rejected();
                        Notification::make()
                            ->danger()
                            ->title('Carry Request Approved')
                            ->sendToDatabase($record->carryRequest->user)
                            ->send();
                    })
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
