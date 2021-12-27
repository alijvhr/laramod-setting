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

    public static function getDriver()
    {
        return env('SETTING_DRIVER', 'db');
    }

    private static function init(): void
    {
        if (self::getDriver() == 'swoole') {
            if (empty(self::$swooleTable)) {
                $settings = self::all();
                $table = self::getSwooleTable();
                foreach ($settings as $item)
                    $table->set($item->key, ['value' => $item->value]);
            }
        } elseif (self::getDriver() == 'redis') {
            $setting = self::first();
            if (Redis::exists($setting->key))
                return;
            $settings = self::all();
            foreach ($settings as $item) {
                Redis::set($item->key, $item->value);
            }
        }
    }

    public static function set(string $key, $value = null): void
    {
        self::init();
        $setting = self::where('key', $key)->count();
        if ($setting) return;
        if (is_array($value)) $value = json_encode($value);
        self::create([
            'key'   => $key,
            'value' => $value
        ]);
        if (self::getDriver() == 'redis') {
            Redis::set($key, $value);
        } elseif (self::getDriver() == 'swoole') {
            self::getSwooleTable()->set($key, ['key' => $key, 'value' => $value]);
        }
        self::getSwooleTable()->set($key, $value);
    }

    public static function get(string $key)
    {
        self::init();
        switch (self::getDriver()) {
            case 'swoole':
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
        }
        $setting = self::where('key', $key)->first();
        if (!$setting)
            return null;
        return $setting->value;
    }

    public static function remove(string $key): void
    {
        self::init();
        self::where('key', $key)->delete();
        $driver = self::getDriver();
        if ($driver == 'swoole') {
            self::init();
            self::getSwooleTable()->del($key);
        } elseif ($driver == 'redis') {
            Redis::del($key);
        }
    }

    public static function exists(string $key): bool
    {
        return (bool)self::where('key', $key)->count();
    }
}
