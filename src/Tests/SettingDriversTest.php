<?php

namespace Sparrow\Setting\Tests;

use Sparrow\Setting\Models\Setting;
use Tests\TestCase;

class SettingDriversTest extends TestCase
{
    private array $drivers = [
        'setting.redis',
        'setting.swoole',
        'setting.db',
    ];

    public function test_can_set_key()
    {
        foreach ($this->drivers as $driver) {
            $driver = Setting::getDriver($driver);
            $driver->set('test', 'test');
            $this->assertEquals('test', $driver->get('test'));
        }
    }

    public function test_can_remove_key()
    {
        foreach ($this->drivers as $driver) {
            $driver = Setting::getDriver($driver);
            $this->assertTrue($driver->exists('test'));
            $driver->remove('test');
            $this->assertFalse($driver->exists('test'));
        }
    }

    public function test_can_increment_key()
    {
        foreach ($this->drivers as $driver) {
            $driver = Setting::getDriver($driver);
            $driver->set('test', 0);
            $driver->incr('test');
            $this->assertEquals(1, $driver->get('test'));
            $driver->remove('test');
        }
    }

    public function test_can_decrement_key()
    {
        foreach ($this->drivers as $driver) {
            $driver = Setting::getDriver($driver);
            $driver->set('test', 1);
            $driver->decr('test');
            $this->assertEquals(0, $driver->get('test'));
            $driver->remove('test');
        }
    }
}
