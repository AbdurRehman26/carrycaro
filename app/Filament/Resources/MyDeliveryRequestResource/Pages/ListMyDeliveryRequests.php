<?php

namespace App\Filament\Resources\MyDeliveryRequestResource\Pages;

use App\Filament\Resources\MyDeliveryRequestResource;
use App\Filament\Traits\DeliveryRequestMethods;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyDeliveryRequests extends ListRecords
{
    use DeliveryRequestMethods;

    protected static string $resource = MyDeliveryRequestResource::class;

    protected static ?string $navigationLabel = 'My Carry Requests';

    protected ?string $heading = 'My Carry Requests';

    protected function getHeaderActions(): array
    {
        return [
            $this->createDeliveryRequestAction(CreateAction::class)->createAnother(false),
        ];
    }
}
