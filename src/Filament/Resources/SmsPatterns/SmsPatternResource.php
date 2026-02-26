<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsPatterns;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Pages\ListSmsPatterns;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Schemas\SmsPatternForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Tables\SmsPatternsTable;
use Mortezaa97\SmsManager\Models\SmsPattern;
use UnitEnum;

class SmsPatternResource extends Resource
{
    protected static ?string $model = SmsPattern::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'الگوهای پیامک';

    protected static ?string $modelLabel = 'الگوی پیامک';

    protected static ?string $pluralModelLabel = 'الگوهای پیامک';

    protected static string|null|UnitEnum $navigationGroup = 'پنل پیامکی';

    public static function form(Schema $schema): Schema
    {
        return SmsPatternForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsPatternsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsPatterns::route('/'),
        ];
    }
}
