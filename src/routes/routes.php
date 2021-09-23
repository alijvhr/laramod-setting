<?php

use Illuminate\Support\Facades\Route;
use Sparrow\Setting\Http\Controllers\SettingController;

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'admin/settings', 'middleware' => ['role:admin']], function () {
        Route::get('/', [SettingController::class, 'index'])->name('site.admin.settings.index');
    });
    Route::post('api/admin/settings/update', [SettingController::class, 'set'])->middleware('role:admin')->name('api.admin.settings.update');
});
