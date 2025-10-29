<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SmsMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('receiver')
                ->label('گیرنده')
                ->translateLabel()->searchable(),
                \App\Filament\Components\Table\SenderTextColumn::create()
                ->label('فرستنده'),
                \Filament\Tables\Columns\TextColumn::make('cost')
                ->suffix(' ریال')
                ->label('هزینه')
                ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('action')
                ->label('علت ارسال')
                ->translateLabel()->searchable(),
                \App\Filament\Components\Table\StatusTextColumn::create(\Mortezaa97\SmsManager\Models\SmsMessage::class)
                ->label('وضعیت'),
                
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->sortable()
                    ->jalaliDateTime('j F Y ساعت H:i'),
            ])
            ->filters([])
            ->recordActions([
                // EditAction removed
            ])
            ->toolbarActions([]);
    }
}

