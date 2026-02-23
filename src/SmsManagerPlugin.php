<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Mortezaa97\SmsManager\Filament\Resources\SmsBlacklists\SmsBlacklistResource;
use Mortezaa97\SmsManager\Filament\Resources\SmsMessages\SmsMessageResource;
use Mortezaa97\SmsManager\Filament\Resources\SmsPatterns\SmsPatternResource;
use Mortezaa97\SmsManager\Filament\Resources\SmsDrivers\SmsDriverResource;
use Mortezaa97\SmsManager\Filament\Widgets\SmsManagerStatsWidget;

class SmsManagerPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'sms-manager';
    }

    public function register(Panel $panel): void
    {
        // To add the SmsManagerStatsWidget to the Dashboard page, 
        // you should add it in the dashboard's getWidgets() method, NOT here.
        // See your app/Filament/Pages/Dashboard.php:
        // public function getWidgets(): array
        // {
        //     return [
        //         ...,
        //         \Mortezaa97\SmsManager\Filament\Widgets\SmsManagerStatsWidget::class,
        //         ...,
        //     ];
        // }
        
        // Optionally, you can still register widgets for custom pages or global dashboard:
        $panel
            ->resources([
                'SmsMessageResource' => SmsMessageResource::class,
                'SmsBlacklistResource' => SmsBlacklistResource::class,
                'SmsPatternResource' => SmsPatternResource::class,
                'SmsDriverResource' => SmsDriverResource::class,
            ])
            ->widgets([
                SmsManagerStatsWidget::class,
            ])->navigationGroups([
                NavigationGroup::make('پنل پیامکی')
                    ->icon('heroicon-o-phone')
                    ->collapsed(),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
