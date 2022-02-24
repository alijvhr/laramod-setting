<?php

namespace Sparrow\Setting\Tests;

use Sparrow\Setting\Models\Setting;
use Tests\TestCase;

class SettingDriversTest extends TestCase
{
    public function test_can_set_key()
    {
        Setting::set('test', 'test');
        $this->assertEquals('test', Setting::get('test'));
    }

    public function test_can_remove_key()
    {
        $this->assertTrue(Setting::exists('test'));
        Setting::remove('test');
        $this->assertFalse(Setting::exists('test'));
    }

    public function test_can_increment_key()
    {
        Setting::set('test', 0);
        Setting::incr('test');
        $this->assertEquals(1, Setting::get('test'));
        Setting::remove('test');
    }

    public function test_can_decrement_key()
    {
        Setting::set('test', 1);
        Setting::decr('test');
        $this->assertEquals(0, Setting::get('test'));
        Setting::remove('test');
    }
}
