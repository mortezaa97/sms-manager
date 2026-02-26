<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SmsBlacklistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \App\Filament\Components\Table\CellphoneTextColumn::create(),
                \Filament\Tables\Columns\TextColumn::make('reason')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('ip')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('user_agent')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('expire_at')->dateTime()->sortable(),
                \App\Filament\Components\Table\CreatedByTextColumn::create(),
                \App\Filament\Components\Table\CreatedAtTextColumn::create(),
                \App\Filament\Components\Table\UpdatedAtTextColumn::create(),
                \App\Filament\Components\Table\DeletedAtTextColumn::create(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
