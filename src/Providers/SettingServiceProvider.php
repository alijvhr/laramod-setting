<?php

namespace Sparrow\Setting\Providers;

use Illuminate\Support\ServiceProvider;
use Sparrow\Setting\Drivers\DatabaseDriver;
use Sparrow\Setting\Drivers\RedisDriver;
use Sparrow\Setting\Drivers\SwooleDriver;
use Sparrow\Setting\Interfaces\SettingDriverProvider;

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
        $this->app->singleton(SettingDriverProvider::class, function ($app) {
            switch ($app->make('config')->get('setting.driver')) {
                case 'redis':
                    return new RedisDriver();
                case 'db':
                    return new DatabaseDriver();
                case 'swoole':
                    return new SwooleDriver();
                default:
                    throw new \RuntimeException('Unknown setting driver used!');
            }
        });
    }
}
