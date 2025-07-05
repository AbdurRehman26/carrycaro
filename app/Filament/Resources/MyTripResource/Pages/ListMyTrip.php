<?php

namespace App\Filament\Resources\MyTripResource\Pages;

use App\Filament\Resources\MyTripResource;
use App\Filament\Traits\TripMethods;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyTrip extends ListRecords
{
    use TripMethods;

    protected static string $resource = MyTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->createTripAction(CreateAction::class)
        ];
    }
}
