<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Mortezaa97\SmsManager\Models\SmsGroup;
use Mortezaa97\SmsManager\Traits\BelongsToSmsGroups;

class AddToSmsGroupAction
{
    /**
     * Record action: add the current record to an SMS group.
     * Use in table recordActions(). The record model must use BelongsToSmsGroups trait.
     *
     * @param  string|null  $cellphoneAttribute  Record attribute for pivot cellphone (e.g. 'cellphone'). Null to show form field.
     */
    public static function makeRecordAction(?string $cellphoneAttribute = 'cellphone'): Action
    {
        return Action::make('addToSmsGroup')
            ->label('افزودن به گروه پیامکی')
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('info')
            ->schema(self::schemaForRecord($cellphoneAttribute))
            ->action(function (array $data, $record) use ($cellphoneAttribute): void {
                if (! self::hasTrait($record)) {
                    Notification::make()
                        ->title('مدل از قابلیت گروه پیامکی پشتیبانی نمی‌کند')
                        ->danger()
                        ->send();

                    return;
                }
                $cellphone = $data['cellphone'] ?? ($cellphoneAttribute ? ($record->{$cellphoneAttribute} ?? null) : null);
                $record->smsGroups()->syncWithoutDetaching([
                    $data['group_id'] => array_filter(['cellphone' => $cellphone]),
                ]);
                Notification::make()
                    ->title('به گروه پیامکی اضافه شد')
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('افزودن');
    }

    /**
     * Bulk action: add selected records to an SMS group.
     * Use in table toolbarActions() (e.g. inside BulkActionGroup). Records must use BelongsToSmsGroups trait.
     *
     * @param  string|null  $cellphoneAttribute  Record attribute for pivot cellphone (e.g. 'cellphone'). Null to not set pivot cellphone.
     */
    public static function makeBulkAction(?string $cellphoneAttribute = 'cellphone'): BulkAction
    {
        return BulkAction::make('addToSmsGroupBulk')
            ->label('افزودن به گروه پیامکی')
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('info')
            ->schema(self::schemaForBulk())
            ->action(function (Collection $records, array $data) use ($cellphoneAttribute): void {
                $groupId = (int) ($data['group_id'] ?? 0);
                if (! $groupId) {
                    Notification::make()
                        ->title('گروه را انتخاب کنید')
                        ->danger()
                        ->send();

                    return;
                }
                $added = 0;
                foreach ($records as $record) {
                    if (! self::hasTrait($record)) {
                        continue;
                    }
                    $cellphone = $cellphoneAttribute ? ($record->{$cellphoneAttribute} ?? null) : null;
                    $record->smsGroups()->syncWithoutDetaching([
                        $groupId => array_filter(['cellphone' => $cellphone]),
                    ]);
                    $added++;
                }
                Notification::make()
                    ->title($added > 0 ? "{$added} رکورد به گروه پیامکی اضافه شد" : 'هیچ رکوردی به گروه اضافه نشد')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->modalSubmitActionLabel('افزودن');
    }

    /**
     * @return array<int, mixed>
     */
    private static function schemaForRecord(?string $cellphoneAttribute): array
    {
        $components = [
            Select::make('group_id')
                ->label('گروه پیامکی')
                ->options(SmsGroup::query()->orderBy('name')->get()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('cellphone')
                ->label('شماره موبایل (اختیاری، برای ذخیره در گروه)')
                ->tel()
                ->placeholder($cellphoneAttribute ? 'خالی = استفاده از شماره رکورد' : '')
                ->default($cellphoneAttribute ? fn ($record) => $record?->{$cellphoneAttribute} ?? null : null),
        ];

        return $components;
    }

    /**
     * @return array<int, mixed>
     */
    private static function schemaForBulk(): array
    {
        return [
            Select::make('group_id')
                ->label('گروه پیامکی')
                ->options(SmsGroup::query()->orderBy('name')->get()->pluck('name', 'id'))
                ->searchable()
                ->required(),
        ];
    }

    private static function hasTrait(object $model): bool
    {
        return in_array(BelongsToSmsGroups::class, class_uses_recursive($model), true);
    }
}
