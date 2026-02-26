<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmptyState;
use Filament\Support\Icons\Heroicon;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\SmsDriverResource;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\Schemas\SmsPatternForm;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\SmsPatternResource;
use Mortezaa97\SmsManager\Models\SmsDriver;

class ListSmsPatterns extends ListRecords
{
    protected static string $resource = SmsPatternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->schema(function (): array {
                    if (! SmsDriver::exists()) {
                        return [EmptyState::make('هیچ درایوری ثبت نشده است')
                            ->description('برای تعریف الگوی پیامک، ابتدا حداقل یک درایور در بخش «درایورهای پیامک» اضافه کنید.')
                            ->icon(Heroicon::OutlinedBuildingOffice)
                            ->footer([
                                Action::make('createDriver')
                                    ->label('ایجاد درایور')
                                    ->icon(Heroicon::Plus)
                                    ->url(SmsDriverResource::getUrl('index')),
                            ])];
                    }

                    return SmsPatternForm::getSchemaComponents();
                })
                ->modalHeading(fn (): string => SmsDriver::exists() ? 'افزودن الگوی پیامک' : 'نیاز به درایور')
                ->modalFooterActions(fn (CreateAction $action): array => SmsDriver::exists()
                    ? [$action->getModalCancelAction(), $action->getModalSubmitAction()]
                    : [$action->getModalCancelAction()->label('بستن')])
                ->action(function (array $data): void {
                    if (! SmsDriver::exists()) {
                        return;
                    }
                    static::getModel()::create($data);
                }),
        ];
    }
}
