<?php

namespace Sparrow\Setting\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'key';
    public $incrementing = false;
    public $timestamps = false;

    public static function getDriver()
    {
        return app()->make(env('SETTING_DRIVER', 'setting.redis'));
    }
}
