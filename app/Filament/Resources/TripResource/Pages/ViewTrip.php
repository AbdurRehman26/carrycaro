<?php

namespace App\Filament\Resources\TripResource\Pages;

use App\Filament\Resources\TripResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTrip extends ViewRecord
{
    protected static string $resource = TripResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Details (Only approved travellers and requesters can see each others details.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('fromCity')->color('primary')->formatStateUsing(fn($record) => $record->fromCity->name  . ' - ' .  $record->fromCity->country->name)->label('From'),
                            TextEntry::make('toCity')->color('primary')->formatStateUsing(fn($record) => $record->toCity->name  . ' - ' .  $record->toCity->country->name)->label('To'),
                            TextEntry::make('departure_date')->color('primary')->date(),
                            TextEntry::make('arrival_date')->color('primary')->date(),
                            TextEntry::make('weight_available')->color('primary')->suffix(' Kg'),
                            TextEntry::make('weight_price')->color('primary'),
                            TextEntry::make('updated_at')->label('Airline')->color('primary')->formatStateUsing(fn ($state, $record) => blank($record->airline) ? 'N/A' : $record->airline),
                            TextEntry::make('id')->label('Notes')->color('primary')->formatStateUsing(fn ($state, $record) => blank($record->notes) ? 'N/A' : $record->notes),
                            TextEntry::make('created_at')->color('primary')->label('Created')->since(),
                            TextEntry::make('user.name')->color('primary')->label('User'),
                            TextEntry::make('user.phone_number')
                                ->formatStateUsing(fn($record, $state) => $record->myApprovedOfferExists() || auth()->user()->id == $record->user_id ? $state : '-')
                                ->color('primary')
                                ->label('Phone Number'),
                            TextEntry::make('user.facebook_profile')
                                ->formatStateUsing(fn($record, $state) => $record->myApprovedOfferExists() || auth()->user()->id == $record->user_id ? $state : '-')
                                ->url(fn ($record, $state) => $record->myApprovedOfferExists() || auth()->user()->id == $record->user_id ? $state : '-')
                                ->icon('heroicon-o-link')
                                ->color('primary')
                                ->label('Facebook Profile'),
                        ]),
                    ]),
            ]);
    }
}
