<?php

namespace App\Filament\Traits;

use App\Models\City;
use App\Models\CarryRequestOffer;
use App\Models\CarryRequest;
use App\Models\CarryRequestProduct;
use App\Models\Product;
use App\Models\Travel;
use Carbon\Carbon;
use Filament\Actions\MountableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;

trait CarryRequestMethods
{
    public function createCarryRequestAction($action)
    {
        return $action::make('add_carry_request')
            ->label('Create Carry Request')
            ->button()
            ->form([
                Toggle::make('add_product')->reactive()->default(true),
                Section::make('Product Information')
                    ->visible(fn($get) => $get('add_product'))
                    ->schema([
                        TextInput::make('product_name')->required(),
                        TextInput::make('product_description')->required(),
                        TextInput::make('product_link')->url()->required(),
                        Fieldset::make()->schema(
                            [
                                TextInput::make('store_name')->required(),
                                TextInput::make('store_location')->required(),
                            ]
                        )->label('Store Information'),
                    ]),
                Section::make('Delivery Information')
                    ->schema([
                        Fieldset::make('')
                            ->schema([
                                Toggle::make('for_self')->columnSpan(2)->reactive()->label('I will receive the package'),

                                TextInput::make('receiver_name')->label('Receiver Name')->visible(fn($get) => !$get('for_self')),
                                TextInput::make('receiver_number')->label('Receiver Number')->visible(fn($get) => !$get('for_self')),

                                TextInput::make('price')->placeholder('Price along with currency (euro, dollar etc)')->label('Price with currency - willing to pay (Approx.)')->required(),
                                TextInput::make('weight')
                                    ->numeric()
                                    ->minValue(0.1)
                                    ->placeholder('Approx Weight (kg)')
                                    ->label('Delivery Weight (Kg)')
                                    ->required(),

                                Select::make('from_city_id')
                                    ->label('City')
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
                                    ->label('From')
                                    ->required(),
                                Select::make('to_city_id')
                                    ->label('City')
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
                                    ->label('To')
                                    ->required(),

                            ])->columnSpan(2),
                        Fieldset::make('Dates between you want your package')->schema([
                            DatePicker::make('preferred_date')->label('Delivery Date (Min.)')->required(),
                            DatePicker::make('delivery_deadline')->label('Delivery Date (Max.)')->required(),
                        ])->columnSpan(2),
                        TextInput::make('note')->placeholder('Additional Info.')->columnSpan(2),

                    ])
            ])
            ->slideOver()
            ->action(function (Travel $travel, array $data) {
                if($data['add_product'] ?? false) {
                    $productData = [
                        'product_name' => $data['product_name'],
                        'product_link' => $data['product_link'],
                        'product_description' => $data['product_description'],
                        'user_id' => auth()->user()->id, // Assuming the user is authenticated
                        'store_name' => $data['store_name'],
                        'store_location' => $data['store_location'],
                        'price' => $data['price'],
                    ];

                    $product = Product::create($productData);
                }


                // Assuming you have a CarryRequest model
                $carryRequest =  \App\Models\CarryRequest::query()->create(
                    array_merge(
                        [
                            'product_id' => isset($product) ? $product->id : null,
                            ...$data
                        ],
                        [
                            'user_id' => auth()->user()->id // Assuming the user is authenticated
                        ]
                    )
                );

                if(! empty($product)){
                    CarryRequestProduct::query()->create([
                        'carry_request_id' => $carryRequest->id,
                        'product_id' => $product->id,
                    ]);
                }

                if(!empty($travel->id)){
                    CarryRequestOffer::query()->create([
                        'travel_id' => $travel->id,
                        'carry_request_id' => $carryRequest->id,
                        'user_id' => auth()->id(),
                    ]);
                }

                \Filament\Notifications\Notification::make()
                    ->title('Carry Request Added')
                    ->body('Your carry request has been added successfully.')
                    ->success()
                    ->send();
            });
    }

    public function iCanBringAction($actionClass): MountableAction
    {
        $travelLists = Travel::query()
            ->where('user_id', auth()->id())
            ->get()
            ->mapWithKeys(function ($record) {
                return [
                    $record->id => "{$record->fromCity->name} ({$record->fromCity->country->name}) to {$record->toCity->name} ({$record->toCity->country->name}) \n ( Departure: " . Carbon::parse($record->departure_date)->toDateString() . ") - ( Arrival: " . Carbon::parse($record->arrival_date)->toDateString(). ")"
                ];
            })->toArray();

        return $actionClass::make('i_can_bring')
            ->authorize(fn($record) => $record->user_id != auth()->id() && !empty($travelLists))
            ->label('I can take with me')
            ->icon('heroicon-o-hand-raised')
            ->color(Color::Purple)
            ->form([
                Select::make('travel_id')
                    ->required()
                    ->label('Select Travel')
                    ->visible(!empty($travelLists))
                    ->options($travelLists)
                    ->searchable(),
                TextInput::make('message')
                    ->visible(!empty($travelLists))
                    ->label('Additional Information')
                    ->placeholder('Any additional information you want to provide'),
            ])

            ->requiresConfirmation()
            ->modalHeading('Confirm Delivery Offer')
            ->modalSubheading('Are you sure you want to opt for bringing this delivery?')
            ->modalButton('Yes, I can take with me')
            ->action(function (array $data, CarryRequest $record) {

                CarryRequestOffer::query()->create([
                    'travel_id' => $data['travel_id'],
                    'carry_request_id' => $record->id,
                    'message' => $data['message'] ?? '',
                    'user_id' => auth()->id(),
                ]);

                Notification::make()
                    ->title('Your Delivery Offer is Created')
                    ->body('Your delivery offer has been successfully created.')
                    ->success()
                    ->send();
            });
    }
}
