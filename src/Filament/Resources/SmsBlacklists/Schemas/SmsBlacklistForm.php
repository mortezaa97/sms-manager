<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Schemas;

use Filament\Schemas\Schema;

class SmsBlacklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Group::make()
                ->schema([
                    \Filament\Schemas\Components\Section::make()
                        ->schema([
                            \App\Filament\Components\Form\CellphoneTextInput::create()->required(),
                            \App\Filament\Components\Form\ReasonTextInput::create()->columnSpan(6),

                        ])
                        ->columns(12)
                        ->columnSpan(12),
                ])
                ->columns(12)
                ->columnSpan(8),
            \Filament\Schemas\Components\Group::make()
                ->schema([
                    \Filament\Schemas\Components\Section::make()
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('ip')->columnSpan(12)->maxLength(255),
                            \Filament\Forms\Components\TextInput::make('user_agent')->columnSpan(12)->maxLength(255),
                            \Filament\Forms\Components\DateTimePicker::make('expire_at')->columnSpan(12)->jalali(),
                            \App\Filament\Components\Form\CreatedBySelect::create(),
                        ])
                        ->columns(12)
                        ->columnSpan(12),
                ])
                ->columns(12)
                ->columnSpan(4),
        ])
            ->columns(12);
    }
}
