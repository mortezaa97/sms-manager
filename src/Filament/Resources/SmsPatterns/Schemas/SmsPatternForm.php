<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Mortezaa97\SmsManager\Models\SmsDriver;

class SmsPatternForm
{
    public static function getSchemaComponents(): array
    {
        return [
            Section::make('الگوی پیامک')
                ->schema([
                    Select::make('driver')
                        ->label('درایور')
                        ->required()
                        ->options(SmsDriver::orderBy('is_default', 'desc')->get()->pluck('title', 'id')),
                    TextInput::make('title')
                        ->label('عنوان')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('کد')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(1),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::getSchemaComponents());
    }
}
