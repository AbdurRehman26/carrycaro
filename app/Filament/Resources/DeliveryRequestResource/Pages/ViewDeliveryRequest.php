<?php

namespace App\Filament\Resources\DeliveryRequestResource\Pages;

use App\Filament\Resources\MyDeliveryRequestResource;
use App\Filament\Traits\DeliveryRequestMethods;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewDeliveryRequest extends ViewRecord
{
    use DeliveryRequestMethods;

    protected static string $resource = MyDeliveryRequestResource::class;

    protected static ?string $title = 'View Carry Request';

    protected function getHeaderActions(): array
    {
        return [
            $this->iCanBringAction(\Filament\Actions\Action::class)->visible(fn(Model $record) => $record->myMatch()->doesntExist())
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Delivery Info')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('fromCity.name')->formatStateUsing(fn(Model $model) => $model->fromCity->name . ' (' . $model->fromCity->country->name . ')')->label('From'),
                            TextEntry::make('toCity.name')->formatStateUsing(fn(Model $model) => $model->toCity->name . ' (' . $model->toCity->country->name . ')')->label('To'),
                            TextEntry::make('preferred_date')->label('Preferred Date')->date(),
                            TextEntry::make('delivery_deadline')->label('Deadline')->date(),
                            TextEntry::make('weight')->suffix(' Kg')->badge(),
                            TextEntry::make('price')->label('Price - willing to pay (Approx.)'),
                            TextEntry::make('created_at')->label('Created')->since(),
                            TextEntry::make('user.name')->label('Created By'),
                            Section::make('Items to Buy')
                                ->visible(fn(Model $record) => $record->products()->count())
                                ->schema([
                                    TextEntry::make('products.product_name')->label('Product Name'),
                                    TextEntry::make('products.product_link')->label('Product Link'),
                                    TextEntry::make('products.product_description')->label('Product Description'),
                                    TextEntry::make('products.store_name')->label('Store Name'),
                                    TextEntry::make('products.store_location')->label('Store Location'),
                                    TextEntry::make('products.price')->label('Price'),
                            ])->columns(4)
                        ]),
                    ]),
            ]);
    }
}
