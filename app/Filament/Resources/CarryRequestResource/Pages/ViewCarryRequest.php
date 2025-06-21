<?php

namespace App\Filament\Resources\CarryRequestResource\Pages;

use App\Filament\Resources\MyCarryRequestResource;
use App\Filament\Traits\CarryRequestMethods;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewCarryRequest extends ViewRecord
{
    use CarryRequestMethods;

    protected static string $resource = MyCarryRequestResource::class;

    protected static ?string $title = 'View Carry Request';

    protected function getHeaderActions(): array
    {
        return [
            $this->iCanBringAction(\Filament\Actions\Action::class)->visible(fn(Model $record) => $record->myOffer()->doesntExist())
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Carry Request Info')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('fromCity.name')->color('primary')->formatStateUsing(fn(Model $model) => $model->fromCity->name . ' (' . $model->fromCity->country->name . ')')->label('From'),
                            TextEntry::make('toCity.name')->color('primary')->formatStateUsing(fn(Model $model) => $model->toCity->name . ' (' . $model->toCity->country->name . ')')->label('To'),
                            TextEntry::make('preferred_date')->color('primary')->label('Preferred Date')->date(),
                            TextEntry::make('delivery_deadline')->color('primary')->label('Deadline')->date(),
                            TextEntry::make('weight')->color('primary')->suffix(' Kg')->badge(),
                            TextEntry::make('price')->color('primary')->label('Price - willing to pay (Approx.)'),
                            TextEntry::make('created_at')->color('primary')->label('Created')->since(),
                            TextEntry::make('user.name')->color('primary')->label('Created By'),
                            Section::make('Items to Buy')
                                ->visible(fn(Model $record) => $record->products()->count())
                                ->schema([
                                    TextEntry::make('products.product_name')->label('Product Name'),
                                    TextEntry::make('products.product_link')
                                        ->formatStateUsing(fn($record) => 'Link')
                                        ->url(fn ($record) => $record->website)
                                        ->url(fn ($record) => $record->products()->first()->product_link)
                                        ->icon('heroicon-o-link')
                                        ->label('Product Link'),
                                    TextEntry::make('products.product_description')
                                        ->limit(50)
                                        ->label('Product Description')
                                        ->tooltip(function (TextEntry $column): ?string {
                                            $state = $column->getState();

                                            if (strlen($state[0]) <= $column->getCharacterLimit()) {
                                                return null;
                                            }

                                            // Only render the tooltip if the column content exceeds the length limit.
                                            return $state[0];
                                        }),
                                    TextEntry::make('products.store_name')->label('Store Name'),
                                    TextEntry::make('products.store_location')->label('Store Location'),
                                    TextEntry::make('products.price')->label('Price'),
                            ])->columns(4)
                        ]),
                    ]),
            ]);
    }
}
