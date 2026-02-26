<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Tables;

use App\Filament\Components\Table\StatusTextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Models\SmsMessage;

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
                StatusTextColumn::create(SmsMessage::class),
                TextColumn::make('created_at')->label('تاریخ')->sortable()->jalaliDateTime('j F Y ساعت H:i')->toggleable(isToggledHiddenByDefault: false),
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
