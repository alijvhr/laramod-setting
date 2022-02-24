<?php

namespace Sparrow\Setting\Drivers;

use Sparrow\Setting\Interfaces\SettingDriverProvider;
use Sparrow\Setting\Models\Setting;
use Swoole\Table;
use SwooleTW\Http\Table\Facades\SwooleTable;

class SwooleDriver implements SettingDriverProvider
{
    private static Table $swooleTable;

    public function __construct()
    {
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
        $setting = Setting::query()->first();
        if (isset($setting) && empty(self::getSwooleTable()->get($setting->key))) {
            $settings = Setting::all();
            $table = self::getSwooleTable();
            foreach ($settings as $item) {
                $table->set($item->key, ['value' => $item->value, 'counter' => $item->counter, 'type' => $item->type]);
            }
        }
    }

    public static function getSwooleTable(): Table
    {
        if (isset(self::$swooleTable)) return self::$swooleTable;
        self::$swooleTable = SwooleTable::get('settings');
        return self::$swooleTable;
    }

    public function get(string $key, $default = null)
    {
        if (!self::getSwooleTable()->exists($key)) {
            return $default;
        }

        return self::getSwooleTable()->get($key);
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
        self::getSwooleTable()->set($key, $fields);
    }

    public function remove(string $key): void
    {
        Setting::query()->where('key', $key)->delete();
        self::getSwooleTable()->del($key);
    }

    public function incr(string $key): int
    {
        $value = self::getSwooleTable()->incr($key, 'counter');
        Setting::query()->where('key', $key)->update(['counter' => $value]);
        return $value;
    }

    public function decr(string $key): int
    {
        $value = self::getSwooleTable()->decr($key, 'counter');
        Setting::query()->where('key', $key)->update(['counter' => $value]);
        return $value;
    }

    public function exists(string $key): bool
    {
        return (bool)Setting::query()->where('key', $key)->count();
    }
}
