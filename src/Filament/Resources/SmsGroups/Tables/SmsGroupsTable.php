<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\SmsGroupResource;

class SmsGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('شناسه')->sortable(),
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('model_has_groups_count')
                    ->label('تعداد اعضا')
                    ->counts('modelHasGroups')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record) => SmsGroupResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
