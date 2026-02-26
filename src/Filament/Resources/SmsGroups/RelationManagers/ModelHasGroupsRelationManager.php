<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsGroups\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModelHasGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'modelHasGroups';

    protected static ?string $title = 'اعضای گروه';

    protected static ?string $recordTitleAttribute = 'model_type';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('model_type')
                    ->label('نوع مدل')
                    ->required()
                    ->maxLength(255),
                TextInput::make('model_id')
                    ->label('شناسه مدل')
                    ->required()
                    ->numeric(),
                TextInput::make('cellphone')
                    ->label('شماره موبایل')
                    ->tel()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('شناسه')->sortable(),
                TextColumn::make('model_type')->label('نوع مدل')->searchable()->sortable(),
                TextColumn::make('model_id')->label('شناسه مدل')->sortable(),
                TextColumn::make('cellphone')->label('شماره موبایل')->searchable(),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make()
                    ->modalHeading('حذف عضو از گروه')
                    ->modalDescription('آیا از حذف این عضو از گروه پیامکی اطمینان دارید؟'),
            ])
            ->toolbarActions([
                \Filament\Actions\DeleteBulkAction::make()
                    ->modalHeading('حذف اعضا از گروه')
                    ->modalDescription('آیا از حذف اعضای انتخاب‌شده از گروه پیامکی اطمینان دارید؟'),
            ]);
    }
}
