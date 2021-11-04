<?php

use Sparrow\Setting\Models\Setting;

function setting(string $key, $default = null)
{
    if (!Setting::exists($key)) return $default;
    return Setting::get($key);
}
