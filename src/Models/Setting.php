<?php

namespace Sparrow\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Sparrow\Setting\Interfaces\SettingDriverProvider;

class Setting extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'key';
    public $incrementing = false;
    public $timestamps = false;

    public static function getDriver(string $key = null): SettingDriverProvider
    {
        return app()->make($key ?? env('SETTING_DRIVER', 'setting.redis'));
    }
}
