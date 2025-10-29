<?php

namespace Mortezaa97\SmsManager\Filament\Resources\SmsMessages\Pages;

use Mortezaa97\SmsManager\Filament\Resources\SmsMessages\SmsMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListSmsMessages extends ListRecords
{
    protected static string $resource = SmsMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction removed
        ];
    }
}

