<?php

namespace Sparrow\Setting\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JetBrains\PhpStorm\ArrayShape;
use Sparrow\Setting\Models\Setting;

class SettingController extends Controller
{
    public function index(): Factory|View|Application
    {
        return view('sparrow-setting::admin.index');
    }

    #[ArrayShape(['status' => "string", 'messages' => "array"])]
    public function set(Request $request): array
    {
        $inputs = $request->except('_token');
        foreach ($inputs as $key => $value)
            Setting::set($key, $value);
        return [
            'status' => 'OK',
            'messages' => [__('admin.settings.success')]
        ];
    }
}
