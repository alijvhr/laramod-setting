<?php

namespace Sparrow\Setting\Providers;

use Illuminate\Support\ServiceProvider;
use Sparrow\Setting\Drivers\DatabaseDriver;
use Sparrow\Setting\Drivers\RedisDriver;
use Sparrow\Setting\Drivers\SwooleDriver;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sparrow-setting');
        $this->loadMigrationsFrom([__DIR__ . '/../database/migrations']);
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/sparrow/support')]);
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'setting');
    }

    public function register()
    {
        $this->app->singleton('setting.redis', fn($app) => new RedisDriver());
        $this->app->singleton('setting.swoole', fn($app) => new SwooleDriver());
        $this->app->singleton('setting.db', fn($app) => new DatabaseDriver());
    }
}
