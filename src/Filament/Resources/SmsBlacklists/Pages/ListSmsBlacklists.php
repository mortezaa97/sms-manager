<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\SmsBlacklistResource;

class ListSmsBlacklists extends ListRecords
{
    protected static string $resource = SmsBlacklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}


