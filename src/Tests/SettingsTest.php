<?php

namespace Sparrow\Setting\Tests;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function test_setting()
    {
        $response = Http::post(route('api.admin.settings.update'), [
            'test_setting' => Str::random()
        ])->json();
        $this->assertTrue($response['status'] == 'OK');
    }
}
