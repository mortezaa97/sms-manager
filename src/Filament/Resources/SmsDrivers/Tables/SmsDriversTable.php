<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Schemas\SmsDriverForm;

class SmsDriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('شناسه')->sortable(),
                TextColumn::make('title')->label('عنوان')->searchable()->sortable(),
                TextColumn::make('sender')->label('فرستنده')->searchable()->sortable(),
                TextColumn::make('api')->label('API')->limit(40)->tooltip(fn ($record) => $record?->api),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean()->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(SmsDriverForm::getSchemaComponents())
                    ->modalHeading('ویرایش درایور پیامک'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
