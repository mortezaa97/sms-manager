<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Schemas\SmsGroupForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\SmsGroupResource;

class ListSmsGroups extends ListRecords
{
    protected static string $resource = SmsGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->form(SmsGroupForm::getSchemaComponents())
                ->modalHeading('افزودن گروه پیامکی'),
        ];
    }
}
