<?php

namespace Sparrow\Setting\Drivers;

use Sparrow\Setting\Interfaces\SettingDriverProvider;
use Sparrow\Setting\Models\Setting;
use Swoole\Table;
use SwooleTW\Http\Table\Facades\SwooleTable;

class SwooleDriver implements SettingDriverProvider
{
    private static Table $swooleTable;

    public static function getSwooleTable(): Table
    {
        if (isset(self::$swooleTable)) return self::$swooleTable;
        self::$swooleTable = SwooleTable::get('settings');
        return self::$swooleTable;
    }

    public function init(): void
    {
        $setting = Setting::query()->first();
        if (isset($setting) && empty(self::getSwooleTable()->get($setting->key))) {
            $settings = Setting::all();
            $table = self::getSwooleTable();
            foreach ($settings as $item) {
                $table->set($item->key, ['value' => $item->value, 'counter' => $item->counter, 'type' => $item->type]);
            }
        }
    }

    public function get(string $key, $default = null)
    {
        self::init();
        if (!self::getSwooleTable()->exists($key)) {
            return $default;
        }

        return self::getSwooleTable()->get($key);
    }

    public function set(string $key, $value, $type = null): void
    {
        self::init();
        if (is_array($value))
            $value = json_encode($value);
        if (is_null($type)) {
            $type = 'string';
            if (filter_var($value, FILTER_VALIDATE_INT)) {
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
        self::getSwooleTable()->set($key, $fields);
    }

    public function remove(string $key): void
    {
        self::init();
        Setting::query()->where('key', $key)->delete();
        self::getSwooleTable()->del($key);
    }

    public function incr(string $key): int
    {
        self::init();
        $value = self::getSwooleTable()->incr($key, 'counter');
        Setting::query()->where('key', $key)->update(['counter' => $value]);
        return $value;
    }

    public function decr(string $key): int
    {
        self::init();
        $value = self::getSwooleTable()->decr($key, 'counter');
        Setting::query()->where('key', $key)->update(['counter' => $value]);
        return $value;
    }

    public function exists(string $key): bool
    {
        return (bool)Setting::query()->where('key', $key)->count();
    }
}
