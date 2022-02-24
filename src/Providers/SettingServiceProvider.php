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
        if (config()->has('swoole_http.tables')) {
            $tables = config('swoole_http.tables');
            $tables['settings'] = [
                'size' => 4096,
                'columns' => [
                    ['name' => 'counter', 'type' => 'int', 'size' => 11],
                    ['name' => 'value', 'type' => 'string', 'size' => 1024],
                    ['name' => 'type', 'type' => 'string', 'size' => 1024]
                ]
            ];
            config(['swoole_http.tables' => $tables]);
        }
    }

    public function register()
    {
        $this->app->bind('setting.redis', fn($app) => new RedisDriver());
        $this->app->bind('setting.swoole', fn($app) => new SwooleDriver());
        $this->app->bind('setting.db', fn($app) => new DatabaseDriver());
    }
}
