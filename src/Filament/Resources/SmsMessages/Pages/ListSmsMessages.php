<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Pages;

use Filament\Resources\Pages\ListRecords;
use Mortezaa97\SmsManager\Filament\Actions\SendSmsAction;
use Mortezaa97\SmsManager\Filament\Resources\SmsMessages\SmsMessageResource;

class ListSmsMessages extends ListRecords
{
    protected static string $resource = SmsMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SendSmsAction::makeHeaderAction(),
        ];
    }
}

