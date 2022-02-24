<?php

namespace Sparrow\Setting\Tests;

use Sparrow\Setting\Interfaces\SettingDriverProvider;
use Tests\TestCase;

class SettingDriversTest extends TestCase
{
    private SettingDriverProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = app()->make(SettingDriverProvider::class);
    }

    public function test_can_set_key()
    {
        $this->provider->set('test', 'test');
        $this->assertEquals('test', $this->provider->get('test'));
    }

    public function test_can_remove_key()
    {
        $this->assertTrue($this->provider->exists('test'));
        $this->provider->remove('test');
        $this->assertFalse($this->provider->exists('test'));
    }

    public function test_can_increment_key()
    {
        $this->provider->set('test', 0);
        $this->provider->incr('test');
        $this->assertEquals(1, $this->provider->get('test'));
        $this->provider->remove('test');
    }

    public function test_can_decrement_key()
    {
        $this->provider->set('test', 1);
        $this->provider->decr('test');
        $this->assertEquals(0, $this->provider->get('test'));
        $this->provider->remove('test');
    }
}
