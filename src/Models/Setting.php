<?php

namespace Sparrow\Setting\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'key';
    public $incrementing = false;
    public $timestamps = false;

    public static function getDriver(string $key = null)
    {
        return $key ?? env('SETTING_DRIVER', 'setting.redis');
    }

    public static function set(string $key, $value, $type = null): void
    {
        app()->make(self::getDriver())->set($key, $value, $type);
    }

    public static function get(string $key, $default = null)
    {
        return app()->make(self::getDriver())->get($key, $default);
    }

    public static function remove(string $key): void
    {
        app()->make(self::getDriver())->remove($key);
    }

    public static function incr(string $key): int
    {
        return app()->make(self::getDriver())->incr($key);
    }

    public static function decr(string $key): int
    {
        return app()->make(self::getDriver())->decr($key);
    }

    public static function exists(string $key): bool
    {
        return app()->make(self::getDriver())->exists($key);
    }
}
