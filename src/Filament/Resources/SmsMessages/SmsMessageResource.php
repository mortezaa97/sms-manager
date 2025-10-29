<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsMessages;

use Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Pages\ListSmsMessages;
use Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Tables\SmsMessagesTable;
use Mortezaa97\SmsManager\Models\SmsMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmsMessageResource extends Resource
{
    protected static ?string $model = SmsMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'پیامک ها';

    protected static ?string $modelLabel = 'پیامک';

    protected static ?string $pluralModelLabel = 'پیامک ها';

    protected static string|null|\UnitEnum $navigationGroup = 'پنل پیامکی';

    public static function table(Table $table): Table
    {
        return SmsMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsMessages::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

