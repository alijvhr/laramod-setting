<?php

namespace Sparrow\Setting\Providers;

use Illuminate\Support\ServiceProvider;
use Swoole\Table;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sparrow-setting');
        $this->loadMigrationsFrom([__DIR__ . '/../database/migrations']);
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/sparrow/support')]);
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'setting');
        $tables = config('swoole_http.tables');
        $tables['settings'] = [
            'size' => 4096,
            'columns' => [
                ['name' => 'counter', 'type' => Table::TYPE_INT, 'size' => 11],
                ['name' => 'value', 'type' => Table::TYPE_STRING, 'size' => 1024],
                ['name' => 'type', 'type' => Table::TYPE_STRING, 'size' => 1024]
            ]
        ];
        config(['swoole_http.tables' => $tables]);
    }
}
