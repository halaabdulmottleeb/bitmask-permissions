<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions;

use Illuminate\Support\ServiceProvider;

class BitmaskPermissionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/bitmask-permissions.php',
            'bitmask-permissions',
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/bitmask-permissions.php' => $this->app->configPath('bitmask-permissions.php'),
            ], 'bitmask-permissions-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'bitmask-permissions-migrations');
        }
    }
}
