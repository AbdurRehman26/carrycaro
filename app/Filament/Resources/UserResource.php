<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\ListUser;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getPages(): array
    {
        return [
            'index' => ListUser::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('phone_number')->searchable(),
            TextColumn::make('facebook_profile')->limit(20)->searchable(),
            TextColumn::make('email_verified_at')->date(),
            TextColumn::make('created_at')->date(),
            TextColumn::make('deleted_at')->date(),
        ]);
    }
}
