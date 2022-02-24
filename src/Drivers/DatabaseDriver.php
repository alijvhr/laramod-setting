<?php

namespace Sparrow\Setting\Drivers;

use Sparrow\Setting\Interfaces\SettingDriverProvider;
use Sparrow\Setting\Models\Setting;

class DatabaseDriver implements SettingDriverProvider
{

    public function init(): void
    {
        //
    }

    public function get(string $key, $default = null)
    {
        $setting = Setting::query()->where('key', $key)->first();
        if (!$setting)
            return $default;
        if ($setting->type == 'int')
            return $setting->counter;
        return $setting->value;
    }

    public function set(string $key, $value, $type = null): void
    {
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
    }

    public function remove(string $key): void
    {
        Setting::query()->where('key', $key)->delete();
    }

    public function incr(string $key): int
    {
        $setting = Setting::query()->where('key', $key)->first();
        $setting->counter++;
        $setting->save();
        return $setting->counter;
    }

    public function decr(string $key): int
    {
        $setting = Setting::query()->where('key', $key)->first();
        $setting->counter--;
        $setting->save();
        return $setting->counter;
    }

    public function exists(string $key): bool
    {
        return (bool)Setting::query()->where('key', $key)->count();
    }
}
