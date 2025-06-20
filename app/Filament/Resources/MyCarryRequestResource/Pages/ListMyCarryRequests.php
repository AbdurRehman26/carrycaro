<?php

namespace App\Filament\Resources\MyCarryRequestResource\Pages;

use App\Filament\Resources\MyCarryRequestResource;
use App\Filament\Traits\CarryRequestMethods;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyCarryRequests extends ListRecords
{
    use CarryRequestMethods;

    protected static string $resource = MyCarryRequestResource::class;

    protected static ?string $navigationLabel = 'My Carry Requests';

    protected ?string $heading = 'My Carry Requests';

    protected function getHeaderActions(): array
    {
        return [
            $this->createCarryRequestAction(CreateAction::class)->createAnother(false),
        ];
    }
}
