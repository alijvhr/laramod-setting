<?php

namespace Sparrow\Setting\Interfaces;

interface SettingDriverProvider
{
    public function init(): void;

    public function get(string $key, $default = null);

    public function set(string $key, $value, $type = null): void;

    public function remove(string $key): void;

    public function incr(string $key): int;

    public function decr(string $key): int;

    public function exists(string $key): bool;
}
