<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsDrivers;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Pages\ListSmsDrivers;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Schemas\SmsDriverForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Tables\SmsDriversTable;
use Mortezaa97\SmsManager\Models\SmsDriver;
use UnitEnum;

class SmsDriverResource extends Resource
{
    protected static ?string $model = SmsDriver::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'درگاه های پیامک';

    protected static ?string $modelLabel = 'درگاه پیامک';

    protected static ?string $pluralModelLabel = 'درگاه های پیامک';

    protected static string|null|UnitEnum $navigationGroup = 'پنل پیامکی';

    public static function form(Schema $schema): Schema
    {
        return SmsDriverForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsDriversTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsDrivers::route('/'),
        ];
    }
}
