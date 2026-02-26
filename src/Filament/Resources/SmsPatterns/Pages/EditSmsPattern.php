<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\SmsPatternResource;

class EditSmsPattern extends EditRecord
{
    protected static string $resource = SmsPatternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
