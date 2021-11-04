<?php

namespace Sparrow\Setting\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Sparrow\Setting\Models\Setting;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sparrow-setting');
        $this->loadMigrationsFrom([__DIR__ . '/../database/migrations']);
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/sparrow/support')
        ]);
        if (Schema::hasTable('settings')) {
            $settings = Setting::all();
            $table    = Setting::getSwooleTable();
            foreach ($settings as $item)
                $table->set($item->key, $item->value);
        }
    }
}
