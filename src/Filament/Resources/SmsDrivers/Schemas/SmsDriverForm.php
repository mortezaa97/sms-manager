<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SmsDriverForm
{
    public static function getSchemaComponents(): array
    {
        return [
            Section::make('درایور پیامک')
                ->schema([
                    TextInput::make('title')
                        ->label('عنوان')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('api')
                        ->label('API')
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('sender')
                        ->label('فرستنده')
                        ->maxLength(255),
                    Toggle::make('is_default')
                        ->label('پیش‌فرض')
                        ->default(false),
                ])
                ->columns(1),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::getSchemaComponents());
    }
}
