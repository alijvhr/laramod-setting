<?php

use Illuminate\Support\Facades\Route;
use Sparrow\Setting\Http\Controllers\SettingController;

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'admin/settings'], function () {
        Route::get('/', [SettingController::class, 'index'])->name('site.admin.settings.index');
    });
    Route::post('api/admin/settings/update', [SettingController::class, 'set'])->name('api.admin.settings.update');
});
