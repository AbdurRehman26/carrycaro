<?php

namespace App\Filament\Traits;

use App\Models\CarryRequest;
use App\Models\CarryRequestOffer;
use App\Models\City;
use App\Models\Trip;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

trait TripMethods
{
    protected function createTripAction($action)
    {
        return
            $action::make('create_trip')
                ->slideOver()
                ->form([
                    Fieldset::make()->schema([
                        Select::make('from_city_id')
                            ->label('From City')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                return City::with('country')
                                    ->where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($city) {
                                        return [$city->id => $city->name . ' (' . $city->country->name . ')'];
                                    })
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $city = City::with('country')->find($value);
                                return $city ? $city->name . ' (' . $city->country->name . ')' : $value;
                            })
                            ->required(),
                        Select::make('to_city_id')
                            ->label('To City')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                return City::with('country')
                                    ->where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($city) {
                                        return [$city->id => $city->name . ' (' . $city->country->name . ')'];
                                    })
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $city = City::with('country')->find($value);
                                return $city ? $city->name . ' (' . $city->country->name . ')' : $value;
                            })
                            ->required(),
                        DatePicker::make('departure_date')
                            ->label('Departure Date')->required(),
                        DatePicker::make('arrival_date')
                            ->label('Arrival Date')->required(),
                        TextInput::make('weight_available')->label('Weight Available (In Kgs)')
                            ->placeholder('Available weight (kg)')
                            ->numeric()
                            ->required()
                            ->minValue(0.1),
                        TextInput::make('weight_price')
                            ->required()
                            ->placeholder('Price per kg (optional) with currency')
                            ->label('Weight Price (with currency)'),
                        TextInput::make('airline')
                            ->label('Airline (optional)'),
                        TextInput::make('notes')
                            ->label('Notes (optional)')
                    ]),
                ])
                ->label('Add Trip Information')
                ->action(function (CarryRequest $carryRequest, array $data) {

                    $fromCountryId = City::query()->find($data['from_city_id'])->country_id;
                    $toCountryId = City::query()->find($data['to_city_id'])->country_id;

                    $trip = Trip::query()->create(
                        array_merge(
                            [
                                ...$data
                            ],
                            [
                                'to_country_id' => $toCountryId,
                                'from_country_id' => $fromCountryId,
                                'user_id' => auth()->user()->id
                            ]
                        ));

                    if(!empty($carryRequest->id)){
                        CarryRequestOffer::query()->create([
                            'trip_id' => $trip->id,
                            'carry_request_id' => $carryRequest->id,
                            'user_id' => auth()->id(),
                        ]);
                    }

                    Notification::make('Trip Information Added')
                        ->body('Your trip information has been added successfully.')
                        ->success()
                        ->send();
                });
    }
}
