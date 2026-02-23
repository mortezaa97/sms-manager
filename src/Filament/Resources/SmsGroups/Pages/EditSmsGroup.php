<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsGroups\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Mortezaa97\SmsManager\Filament\Resources\SmsGroups\SmsGroupResource;

class EditSmsGroup extends EditRecord
{
    protected static string $resource = SmsGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
