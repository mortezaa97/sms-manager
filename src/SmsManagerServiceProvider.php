<?php

namespace Mortezaa97\SmsManager;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Mortezaa97\SmsManager\Models\SmsBlacklist;
use Mortezaa97\SmsManager\Models\SmsMessage;
use Mortezaa97\SmsManager\Models\SmsPattern;
use Mortezaa97\SmsManager\Models\SmsDriver;
use Mortezaa97\SmsManager\Models\SmsGroup;
use Mortezaa97\SmsManager\Models\SmsModelHasGroup;
use Mortezaa97\SmsManager\Policies\SmsBlacklistPolicy;
use Mortezaa97\SmsManager\Policies\SmsMessagePolicy;
use Mortezaa97\SmsManager\Policies\SmsPatternPolicy;
use Mortezaa97\SmsManager\Policies\SmsDriverPolicy;
use Mortezaa97\SmsManager\Policies\SmsGroupPolicy;
use Mortezaa97\SmsManager\Policies\SmsModelHasGroupPolicy;

class SmsManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        Gate::policy(SmsMessage::class, SmsMessagePolicy::class);
        Gate::policy(SmsBlacklist::class, SmsBlacklistPolicy::class);
        Gate::policy(SmsPattern::class, SmsPatternPolicy::class);
        Gate::policy(SmsDriver::class, SmsDriverPolicy::class);
        Gate::policy(SmsGroup::class, SmsGroupPolicy::class);
        Gate::policy(SmsModelHasGroup::class, SmsModelHasGroupPolicy::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('sms-manager.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'sms-manager-migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sms-manager');

        // Register the main class to use with the facade
        $this->app->singleton('sms-manager', function () {
            return new SmsManager;
        });
        $this->app->alias('sms-manager', SmsManager::class);
    }
}
