<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\Schemas\SmsDriverForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\SmsDriverResource;

class ListSmsDrivers extends ListRecords
{
    protected static string $resource = SmsDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->form(SmsDriverForm::getSchemaComponents())
                ->modalHeading('افزودن درایور پیامک'),
        ];
    }
}
