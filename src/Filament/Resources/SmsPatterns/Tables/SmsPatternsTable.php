<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Schemas\SmsPatternForm;

class SmsPatternsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('شناسه')->sortable(),
                TextColumn::make('driver')->label('درایور')->searchable()->sortable(),
                TextColumn::make('title')->label('عنوان')->searchable()->sortable(),
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(SmsPatternForm::getSchemaComponents())
                    ->modalHeading('ویرایش الگوی پیامک'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
