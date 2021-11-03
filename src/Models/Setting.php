<?php

namespace Sparrow\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Swoole\Table;
use SwooleTW\Http\Table\Facades\SwooleTable;

class Setting extends Model
{
    protected $guarded      = [];
    protected $primaryKey   = 'key';
    public    $incrementing = false;
    public    $timestamps   = false;
    private static Table $swooleTable;

    public static function getSwooleTable(): Table
    {
        if (isset(self::$swooleTable)) return self::$swooleTable;
        self::$swooleTable = SwooleTable::get('settings');
        return self::$swooleTable;
    }

    public static function set(string $key, $value = null): void
    {
        $setting = self::where('key', $key)->count();
        if ($setting) return;
        if (is_array($value)) $value = json_encode($value);
        Setting::create([
            'key'   => $key,
            'value' => $value
        ]);
        self::getSwooleTable()->set($key, $value);
    }

    public static function get(string $key)
    {
        if (!self::getSwooleTable()->exists($key)) return null;
        return self::getSwooleTable()->get($key);
    }

    public static function remove(string $key): void
    {
        self::where('key', $key)->delete();
        self::getSwooleTable()->del($key);
    }

    public static function exists(string $key): bool
    {
        return self::getSwooleTable()->exists($key);
    }
}
