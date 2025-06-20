<?php

namespace App\Filament\Resources\MyTravelResource\Pages;

use App\Filament\Resources\MyTravelResource;
use App\Filament\Traits\TravelMethods;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyTravel extends ListRecords
{
    use TravelMethods;

    protected static string $resource = MyTravelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->createTravelAction(CreateAction::class)
        ];
    }
}
