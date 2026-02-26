<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Pages;

use Filament\Resources\Pages\CreateRecord;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\SmsBlacklistResource;

class CreateSmsBlacklist extends CreateRecord
{
    protected static string $resource = SmsBlacklistResource::class;
}
