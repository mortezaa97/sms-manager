<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Mortezaa97\SmsManager\Models\SmsDriver;
use Mortezaa97\SmsManager\Models\SmsPattern;
use Mortezaa97\SmsManager\SmsManager;

class SendSmsAction
{
    /**
     * Record action: send SMS to the current record (e.g. one user).
     * Use in table recordActions(). Receptor is taken from record attribute (default: cellphone).
     */
    public static function makeRecordAction(?string $receptorAttribute = null): Action
    {
        $receptorAttribute = $receptorAttribute ?? config('sms-manager.receptor_attribute', 'cellphone');

        return Action::make('sendSms')
            ->label('ارسال پیامک')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->schema(self::schema(false))
            ->action(function (array $data, $record) use ($receptorAttribute): void {
                $phone = $record?->{$receptorAttribute} ?? null;
                if (empty($phone)) {
                    Notification::make()
                        ->title('شماره گیرنده موجود نیست')
                        ->danger()
                        ->send();
                    return;
                }
                self::sendOne($phone, $data);
            })
            ->modalSubmitActionLabel('ارسال');
    }

    /**
     * Bulk action: send SMS to all selected records.
     * Use inside BulkActionGroup in table toolbarActions(). Receptor from each record (default: cellphone).
     */
    public static function makeBulkAction(?string $receptorAttribute = null): BulkAction
    {
        $receptorAttribute = $receptorAttribute ?? config('sms-manager.receptor_attribute', 'cellphone');

        return BulkAction::make('sendSmsBulk')
            ->label('ارسال پیامک به انتخاب‌شده‌ها')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->schema(self::schema(false))
            ->action(function (Collection $records, array $data) use ($receptorAttribute): void {
                $receptors = $records->pluck($receptorAttribute)->filter()->values()->all();
                if (empty($receptors)) {
                    Notification::make()
                        ->title('هیچ شماره‌ای در رکوردهای انتخاب شده یافت نشد')
                        ->danger()
                        ->send();
                    return;
                }
                self::sendMany($receptors, $data);
            })
            ->deselectRecordsAfterCompletion()
            ->modalSubmitActionLabel('ارسال');
    }

    /**
     * Standalone/header action: send SMS to custom receptor(s).
     * Use in getHeaderActions() or anywhere. Form includes receptor field (comma-separated for multiple).
     */
    public static function makeHeaderAction(): Action
    {
        return Action::make('sendSmsHeader')
            ->label('ارسال پیامک')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->schema(self::schema(true))
            ->action(function (array $data): void {
                $receptor = $data['receptor'] ?? '';
                $receptors = array_filter(array_map('trim', explode(',', $receptor)));
                if (empty($receptors)) {
                    Notification::make()
                        ->title('حداقل یک شماره گیرنده وارد کنید')
                        ->danger()
                        ->send();
                    return;
                }
                if (count($receptors) === 1) {
                    self::sendOne($receptors[0], $data);
                } else {
                    self::sendMany($receptors, $data);
                }
            })
            ->modalSubmitActionLabel('ارسال');
    }

    /**
     * @return array<int, mixed>
     */
    private static function schema(bool $includeReceptor): array
    {
        $components = [];
        if ($includeReceptor) {
            $components[] = TextInput::make('receptor')
                ->label('شماره گیرنده (چند شماره با کاما جدا کنید)')
                ->placeholder('09121234567')
                ->required();
        }
        $components[] = Select::make('driver_id')
            ->label('درگاه پیامکی')
            ->options(SmsDriver::query()->orderByDesc('is_default')->get()->pluck('title', 'id'))
            ->searchable()
            ->default(fn () => SmsDriver::where('is_default', true)->first()?->id ?? SmsDriver::query()->first()?->id)
            ->live()
            ->afterStateUpdated(fn (callable $set) => $set('pattern_id', null));
        $components[] = Select::make('pattern_id')
            ->label('الگوی پیامک')
            ->placeholder('بدون الگو')
            ->options(function (Get $get): array {
                $driverId = $get('driver_id');
                if (! filled($driverId)) {
                    return [];
                }
                return SmsPattern::query()
                    ->where('driver', $driverId)
                    ->get()
                    ->pluck('title', 'id')
                    ->all();
            })
            ->preload()
            ->noOptionsMessage('بدون الگو')
            ->searchable()
            ->live();
        $components[] = Textarea::make('message')
            ->label('متن پیامک')
            ->hint(fn (Get $get): string => $get('pattern_id') ? 'الگوی پیامک: ' . SmsPattern::find($get('pattern_id'))->title : '')
            ->required(fn (Get $get): bool => ! filled($get('pattern_id')))
            ->disabled(fn (Get $get): bool => filled($get('pattern_id')))
            ->default(fn (Get $get): string => $get('pattern_id') ? SmsPattern::find($get('pattern_id'))->message : '')
            ->maxLength(900)
            ->rows(4);
        return $components;
    }

    /**
     * @param  array{message: string, sender?: string|null, driver_id?: int|null, pattern_id?: int|null}  $data
     */
    private static function sendOne(string $receptor, array $data): void
    {
        $manager = app('sms-manager');
        $sender = ! empty($data['sender']) ? $data['sender'] : null;
        $driverId = isset($data['driver_id']) ? (int) $data['driver_id'] : null;
        $patternId = isset($data['pattern_id']) ? (int) $data['pattern_id'] : null;
        try {
            $manager->send($receptor, $data['message'], $sender, 'manual', $driverId, $patternId);
            Notification::make()
                ->title('پیامک ارسال شد')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('خطا در ارسال پیامک')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * @param  array<int, string>  $receptors
     * @param  array{message: string, sender?: string|null, driver_id?: int|null, pattern_id?: int|null}  $data
     */
    private static function sendMany(array $receptors, array $data): void
    {
        $manager = app('sms-manager');
        $sender = ! empty($data['sender']) ? $data['sender'] : null;
        $driverId = isset($data['driver_id']) ? (int) $data['driver_id'] : null;
        $patternId = isset($data['pattern_id']) ? (int) $data['pattern_id'] : null;
        try {
            $manager->sendToMany($receptors, $data['message'], $sender, 'manual', $driverId, $patternId);
            Notification::make()
                ->title('پیامک‌ها ارسال شد')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('خطا در ارسال پیامک')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
