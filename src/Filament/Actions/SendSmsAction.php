<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
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
        $components[] = Textarea::make('message')
            ->label('متن پیامک')
            ->required()
            ->maxLength(900)
            ->rows(4);
        $components[] = TextInput::make('sender')
            ->label('خط فرستنده (اختیاری)')
            ->placeholder(config('sms-manager.drivers.kavenegar.sender') ?? '');

        return $components;
    }

    /**
     * @param  array{message: string, sender?: string|null}  $data
     */
    private static function sendOne(string $receptor, array $data): void
    {
        $manager = app('sms-manager');
        $sender = ! empty($data['sender']) ? $data['sender'] : null;
        try {
            $manager->send($receptor, $data['message'], $sender, 'manual');
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
     * @param  array{message: string, sender?: string|null}  $data
     */
    private static function sendMany(array $receptors, array $data): void
    {
        $manager = app('sms-manager');
        $sender = ! empty($data['sender']) ? $data['sender'] : null;
        try {
            $manager->sendToMany($receptors, $data['message'], $sender, 'manual');
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
