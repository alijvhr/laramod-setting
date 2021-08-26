<?php

namespace Sparrow\Setting\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class Setting
{
    private static int $expirationMinutes = 15;

    public static function set(string $key, mixed $value = null): void
    {
        if (Cache::has($key) || $key == 'all_keys')
            return;

        if (is_array($value))
            $value = json_encode($value);

        Cache::forever($key, $value);
        Redis::set($key, $value, now()->diffInSeconds(now()->addMinutes(self::$expirationMinutes)));
    }

    public static function remove(string $key): void
    {
        Cache::forget($key);
        Redis::del($key);
    }

    public static function get(string $key): mixed
    {
        if (!Cache::offsetExists($key) || $key == 'all_keys') return null;
        if (!Redis::exists($key)) Redis::set($key, Cache::get($key), now()->diffInSeconds(now()->addMinutes(self::$expirationMinutes)));

        return Redis::get($key);
    }
}
