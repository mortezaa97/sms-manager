<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                        ->label('درگاه پیامک')
                        ->required()
                        ->options(SmsDriver::orderBy('is_default', 'desc')->get()->pluck('title', 'id')),
                    TextInput::make('title')
                        ->label('عنوان')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('کد')
                        ->maxLength(255)
                        ->placeholder('در صورت خالی بودن، متن پیامک وارد شود')
                        ->live(),
                    Textarea::make('message')
                        ->label('متن پیامک')
                        ->rows(4)
                        ->maxLength(900)
                        ->disabled(fn ($get) => filled($get('code')))
                        ->required(fn ($get) => ! filled($get('code'))),
                ])
                ->columns(1),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::getSchemaComponents());
    }
}
