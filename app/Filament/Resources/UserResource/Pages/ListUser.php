<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUser extends ListRecords
{
    protected static string $resource = UserResource::class;
}
