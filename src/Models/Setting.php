<?php

namespace Sparrow\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Swoole\Table;
use SwooleTW\Http\Table\Facades\SwooleTable;

class Setting extends Model
{
    protected            $guarded      = [];
    protected            $primaryKey   = 'key';
    public               $incrementing = false;
    public               $timestamps   = false;
    private static Table $swooleTable;

    public static function getSwooleTable(): Table
    {
        if (isset(self::$swooleTable)) return self::$swooleTable;
        self::$swooleTable = SwooleTable::get('settings');
        return self::$swooleTable;
    }

    public static function getDriver() {
        return env('SETTING_DRIVER', 'db');
    }

    private static function init(): void
    {
        if (empty(self::$swooleTable)) {
            $settings = Setting::all();
            $table    = Setting::getSwooleTable();
            foreach ($settings as $item)
                $table->set($item->key, $item->value);
        }
    }

    public static function set(string $key, $value = null): void
    {
        self::init();
        $setting = self::where('key', $key)->count();
        if ($setting) return;
        if (is_array($value)) $value = json_encode($value);
        Setting::create([
            'key'   => $key,
            'value' => $value
        ]);
        if (self::getDriver() == 'redis') {
            Redis::set($key, $value);
            Redis::expire($key, now()->diffInSeconds(now()->addMinutes(env('SETTING_TTL', 1))));
        }
        self::getSwooleTable()->set($key, $value);
    }

    public static function get(string $key)
    {
        switch (self::getDriver()) {
            case 'swoole':
                self::init();
                if (!self::getSwooleTable()->exists($key)) return null;
                return self::getSwooleTable()->get($key);
            case 'redis':
                if (Redis::exists($key))
                    return Redis::get($key);
                if (($setting = self::where('key', $key)->first())) {
                    self::set($key, $setting->value);
                    return Redis::get($key);
                }
                return null;
            case 'db':
                $setting = self::where('key', $key)->first();
                if (!$setting)
                    return null;
                return $setting->value;
        }
    }

    public static function remove(string $key): void
    {
        self::where('key', $key)->delete();
        $driver = self::getDriver();
        if ($driver == 'swoole') {
            self::init();
            self::getSwooleTable()->del($key);
        }
        elseif ($driver == 'redis') {
            Redis::del($key);
        }
    }

    public static function exists(string $key): bool
    {
        return (bool)self::where('key', $key)->count();
    }
}
