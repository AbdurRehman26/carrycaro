<?php

namespace App\Filament\Resources\TripResource\Pages;

use App\Filament\Resources\TripResource;
use App\Filament\Traits\CarryRequestMethods;
use App\Filament\Traits\TripMethods;
use App\Models\City;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListTrip extends ListRecords
{
    use TripMethods, CarryRequestMethods;

    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->createTripAction(CreateAction::class)->createAnother(false)
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
//                TextColumn::make('user.name')
//                    ->size('lg')
//                    ->label('Traveler')
//                    ->icon('heroicon-o-user-circle')->extraAttributes([
//                        'class' => 'mb-4 font-bold',
//                    ]),
                Stack::make([
                    Split::make([
                        TextColumn::make('fromCity.name')
                            ->icon('fas-plane-departure')
                            ->label('From City'),
                        TextColumn::make('fromCity.country.name')
                            ->icon('heroicon-o-globe-americas')
                            ->label('From Country'),
                    ]),
                    Split::make([
                        TextColumn::make('toCity.name')
                            ->icon('fas-plane-arrival')
                            ->label('To City'),
                        TextColumn::make('toCity.country.name')
                            ->icon('heroicon-o-globe-asia-australia')
                            ->label('To Country'),
                    ]),

                    Split::make([
                        TextColumn::make('departure_date')
                            ->date()
                            ->label('Departure Date'),
                        TextColumn::make('arrival_date')
                            ->date()
                            ->label('Arrival Date'),
                    ]),

                    Split::make([
                        TextColumn::make('weight_available')->suffix(' Kg')->prefix('Weight Available: ')
                            ->badge()->label('Available Weight'),
                        TextColumn::make('weight_price')->badge()->prefix('Price per weight: '),
                    ]),

                    TextColumn::make('offers_count')
                        ->suffix('  carry requests')
                        ->badge()
                        ->color('info')
//                        ->url(fn($record) => TravelResource::getUrl('view', [
//                            'record' => $record->id,
//                        ]))
                        ->counts('offers'),


                    TextColumn::make('airline')
                        ->label('Airline'),
                    TextColumn::make('notes')
                        ->limit(50)
                        ->label('Notes'),
                ])->space(3),
            ])->actions([
                $this->createCarryRequestAction(Action::class)->color('info')
                    ->visible(fn ($record) => $record->user_id !== auth()->user()->id && $record
                            ->join('carry_request_offers', 'carry_request_offers.trip_id', 'trips.id')
                            ->join('carry_requests', 'carry_requests.id', 'carry_request_offers.carry_request_id')
                            ->where('carry_requests.user_id', auth()->user()->id)
                            ->orWhere('carry_request_offers.user_id', auth()->user()->id)
                            ->doesntExist()),

                Action::make('view_details')
                    ->label('View Details')
                    ->url(fn($record) => TripResource::getUrl('view',
                        [
                            'record' => $record
                        ]
                    ))
                    ->button()
                    ->color('info')
            ])->actionsPosition(ActionsPosition::BeforeColumns)
            ->filters([
                SelectFilter::make('from_city_id')
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
                    })->getOptionLabelUsing(function ($value): string {
                        $city = City::with('country')->find($value);
                        return $city ? $city->name . ' (' . $city->country->name . ')' : $value;
                    })->query(function($query, array $data){
                        if(empty($data['value'])){
                            return $query;
                        }

                        return $query->where('from_city_id', $data['value']);
                    }),
                SelectFilter::make('to_city_id')
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
                    })->getOptionLabelUsing(function ($value): string {
                        $city = City::with('country')->find($value);
                        return $city ? $city->name . ' (' . $city->country->name . ')' : $value;
                    })->query(function($query, array $data){
                        if(empty($data['value'])){
                            return $query;
                        }

                        return $query->where('to_city_id', $data['value']);
                    }),

                Filter::make('departure_date')
                    ->form([
                        DatePicker::make('departure_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['departure_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_date', '=', $date),
                            );
                    }),
                Filter::make('arrival_date')
                    ->form([
                        DatePicker::make('arrival_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['arrival_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('arrival_date', '=', $date),
                            );
                    })
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->contentGrid([
                'md' => 3,
                'xl' => 3,
            ]);
    }
}
