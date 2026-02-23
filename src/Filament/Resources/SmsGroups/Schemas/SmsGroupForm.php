<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SmsGroupForm
{
    public static function getSchemaComponents(): array
    {
        return [
            Section::make('گروه پیامکی')
                ->schema([
                    TextInput::make('name')
                        ->label('نام')
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
