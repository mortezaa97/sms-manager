<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Pages\CreateSmsBlacklist;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Pages\EditSmsBlacklist;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Pages\ListSmsBlacklists;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Schemas\SmsBlacklistForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Tables\SmsBlacklistsTable;
use Mortezaa97\SmsManager\Models\SmsBlacklist;

class SmsBlacklistResource extends Resource
{
    protected static ?string $model = SmsBlacklist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'لیست سیاه';
    protected static ?string $navigationLabel = 'لیست سیاه';

    protected static ?string $modelLabel = 'لیست سیاه';

    protected static ?string $pluralModelLabel = 'لیست سیاه';

    protected static string|null|\UnitEnum $navigationGroup = 'پنل پیامکی';

    public static function form(Schema $schema): Schema
    {
        return SmsBlacklistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsBlacklistsTable::configure($table);
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
            'index' => ListSmsBlacklists::route('/'),
            'create' => CreateSmsBlacklist::route('/create'),
            'edit' => EditSmsBlacklist::route('/{record}/edit'),
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


