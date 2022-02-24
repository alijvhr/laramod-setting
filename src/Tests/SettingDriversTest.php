<?php

namespace Sparrow\Setting\Tests;

use Sparrow\Setting\Models\Setting;
use Tests\TestCase;

class SettingDriversTest extends TestCase
{
    public function test_can_set_key()
    {
        $driver = Setting::getDriver();
        $driver->set('test', 'test');
        $this->assertEquals('test', $driver->get('test'));
    }

    public function test_can_remove_key()
    {
        $driver = Setting::getDriver();
        $this->assertTrue($driver->exists('test'));
        $driver->remove('test');
        $this->assertFalse($driver->exists('test'));
    }

    public function test_can_increment_key()
    {
        $driver = Setting::getDriver();
        $driver->set('test', 0);
        $driver->incr('test');
        $this->assertEquals(1, $driver->get('test'));
        $driver->remove('test');
    }

    public function test_can_decrement_key()
    {
        $driver = Setting::getDriver();
        $driver->set('test', 1);
        $driver->decr('test');
        $this->assertEquals(0, $driver->get('test'));
        $driver->remove('test');
    }
}
