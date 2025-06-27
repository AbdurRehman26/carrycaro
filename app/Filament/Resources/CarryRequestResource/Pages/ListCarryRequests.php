<?php

namespace App\Filament\Resources\CarryRequestResource\Pages;

use App\Filament\Resources\CarryRequestResource;
use App\Filament\Traits\TripMethods;
use App\Models\City;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\CarryRequestMethods;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListCarryRequests extends ListRecords
{
    use CarryRequestMethods, TripMethods;

    protected static string $resource = CarryRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->createCarryRequestAction(CreateAction::class)->createAnother(false),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ViewColumn::make('profile_card')
                        ->view('filament.cards.carry-request')
                        ->extraAttributes([
                            'class' => 'text-center',
                        ])
                ]),
            ])
            ->recordUrl(function(){

            })
            ->actions([
                Action::make('view_details')
                    ->label('View Details')
                    ->url(fn($record) => CarryRequestResource::getUrl('view',
                        [
                            'record' => $record
                        ]
                    ))
                    ->button()
                    ->color('info'),
                $this->createTripAction(Action::class)->label('Add Trip Info and Request')->visible(fn(Model $record) => $record->user_id != auth()->user()->id &&  auth()->user()->trips()->doesntExist()),
                $this->iCanBringAction(Action::class)->visible(fn(Model $record) => $record->myOffer()->doesntExist()),
                Action::make('view_your_offer')
                    ->label('Already Offered')
                    ->disabled()
                    ->visible(fn(Model $record) => $record->myOffer()->exists())
                    ->color('info'),
            ])
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
                    })->query(function (Builder $query, array $data) {
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
                    })->query(function (Builder $query, array $data) {
                        if(empty($data['value'])){
                            return $query;
                        }

                        return $query->where('to_city_id', $data['value']);
                    }),

                Filter::make('carry_date')
                    ->form([
                        DatePicker::make('carry_date')->label('Delivery Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['carry_date'],
                                fn (Builder $query, $date): Builder => $query->where('preferred_date', '>=', $date)->orWhere('delivery_deadline', '>=', $date),
                            );
                    }),

            ], FiltersLayout::AboveContent)
            ->contentGrid([
                'md' => 3,
                'xl' => 3,
            ]);
    }
}
