<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsGroups;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Pages\EditSmsGroup;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Pages\ListSmsGroups;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\RelationManagers\ModelHasGroupsRelationManager;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Schemas\SmsGroupForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Tables\SmsGroupsTable;
use Mortezaa97\SmsManager\Models\SmsGroup;

class SmsGroupResource extends Resource
{
    protected static ?string $model = SmsGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'گروه‌های پیامکی';

    protected static ?string $modelLabel = 'گروه پیامکی';

    protected static ?string $pluralModelLabel = 'گروه‌های پیامکی';

    protected static string|null|\UnitEnum $navigationGroup = 'پنل پیامکی';

    public static function form(Schema $schema): Schema
    {
        return SmsGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsGroupsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsGroups::route('/'),
            'edit' => EditSmsGroup::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ModelHasGroupsRelationManager::class,
        ];
    }
}
