<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
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
                TextColumn::make('sender')->label('فرستنده')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('username')->label('نام کاربری')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('api')->label('API')->limit(40)->tooltip(fn ($record) => $record?->api),
                IconColumn::make('allow_single')->label('تکی')->boolean()->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('allow_bulk')->label('گروهی')->boolean()->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('allow_pattern')->label('الگو')->boolean()->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(SmsDriverForm::getSchemaComponents())
                    ->modalHeading('ویرایش درایور پیامک'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
