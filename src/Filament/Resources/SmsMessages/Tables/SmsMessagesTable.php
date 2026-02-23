<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SmsMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['driver', 'pattern']))
            ->columns([
                TextColumn::make('id')->label('شناسه')->sortable(),
                TextColumn::make('receiver')->label('گیرنده')->searchable()->sortable(),
                TextColumn::make('message')->label('متن')->limit(40)->tooltip(fn ($record) => $record?->message)->searchable(),
                TextColumn::make('sender')->label('فرستنده')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('driver.title')->label('درایور')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pattern.title')->label('الگو')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cost')->label('هزینه')->suffix(' ریال')->sortable(),
                TextColumn::make('action')->label('علت ارسال')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->label('وضعیت')->badge()->sortable(),
                TextColumn::make('created_at')->label('تاریخ')->sortable()->dateTime(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

