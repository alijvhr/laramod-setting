<?php

namespace Sparrow\Setting\Drivers;

use Illuminate\Support\Facades\Redis;
use Sparrow\Setting\Interfaces\SettingDriverProvider;
use Sparrow\Setting\Models\Setting;

class RedisDriver implements SettingDriverProvider
{
    public function get(string $key, $default = null)
    {
        self::init();
        if (Redis::exists($key))
            return Redis::get($key);
        if (($setting = Setting::where('key', $key)->first())) {
            self::set($key, $setting->value);
            return Redis::get($key);
        }
        return $default;
    }

    public function init(): void
    {
        $setting = Setting::query()->first();
        if (Redis::exists($setting->key))
            return;
        $settings = Setting::all();
        foreach ($settings as $item) {
            Redis::set($item->key, $item->value);
        }
    }

    public function set(string $key, $value, $type = null): void
    {
        self::init();
        if (is_array($value))
            $value = json_encode($value);
        if (is_null($type)) {
            $type = 'string';
            if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                $type = 'int';
            }
        }
        $fields = [
            'value' => $type == 'string' ? $value : '-',
            'counter' => $type == 'int' ? $value : 0,
            'type' => $type
        ];
        Setting::query()->updateOrCreate(
            ['key' => $key],
            $fields
        );
        Redis::set($key, $value);
    }

    public function remove(string $key): void
    {
        self::init();
        Setting::query()->where('key', $key)->delete();
        Redis::del($key);
    }

    public function incr(string $key): int
    {
        self::init();
        $setting = Setting::query()->where('key', $key)->first();
        $setting->counter++;
        $setting->save();
        self::set($key, $setting->counter);
        return $setting->counter;
    }

    public function decr(string $key): int
    {
        self::init();
        $setting = Setting::query()->where('key', $key)->first();
        $setting->counter--;
        $setting->save();
        self::set($key, $setting->counter);
        return $setting->counter;
    }

    public function exists(string $key): bool
    {
        self::init();
        if (!Redis::exists($key)) {
            return (bool)Setting::query()->where('key', $key)->count();
        }
        return true;
    }
}
