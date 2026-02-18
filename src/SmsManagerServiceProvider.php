<?php

namespace Mortezaa97\SmsManager;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Mortezaa97\SmsManager\Models\SmsBlacklist;
use Mortezaa97\SmsManager\Models\SmsMessage;
use Mortezaa97\SmsManager\Policies\SmsBlacklistPolicy;
use Mortezaa97\SmsManager\Policies\SmsMessagePolicy;

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

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('sms-manager.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migrations');
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
