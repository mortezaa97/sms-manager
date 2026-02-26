<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\SmsBlacklistResource;

class EditSmsBlacklist extends EditRecord
{
    protected static string $resource = SmsBlacklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
