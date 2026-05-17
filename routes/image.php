<?php

use Illuminate\Support\Facades\Route;
use Hexatex\LaravelImage\Http\Controllers\Admin;
use Hexatex\LaravelImage\Http\Controllers\Web;

Route::bindImageModel();

// Admin Routes
Route::middleware('can:admin-features.access')->group(function () {

    // Api
    Route::prefix('admin/api')->middleware('api')->group(function () {
        Route::post('image/index', [Admin\Api\ImageController::class, 'index'])->middleware('can:image.index')->name('admin.image.index');
        Route::get('image/{image}', [Admin\Api\ImageController::class, 'get'])->middleware('can:view,image')->name('admin.image.get');
        Route::post('image', [Admin\Api\ImageController::class, 'store'])->middleware('can:image.store')->name('admin.image.store');
        Route::put('image/{image}', [Admin\Api\ImageController::class, 'update'])->middleware('can:update,image')->name('admin.image.update');
        Route::delete('image/{image}', [Admin\Api\ImageController::class, 'destroy'])->middleware('can:image.destroy')->name('admin.image.destroy');
    });

    // Web
    Route::prefix('admin')->middleware('web')->group(function () {
        Route::get('image', [Admin\ImageController::class, 'index'])->middleware('can:image.index')->name('admin.web.image.index');
        Route::get('image/{image}', [Admin\ImageController::class, 'show'])->middleware('can:view,image')->name('admin.web.image.show');
    });
});

// Public Web Routes
Route::middleware('web')->group(function () {
    Route::get('image/{image}', [Web\ImageController::class, 'download'])->middleware(['can:download,image', 'signed'])->name('image.download');
    Route::get('image/{image}/thumbnail', [Web\ImageController::class, 'downloadThumbnail'])->middleware(['can:download,image', 'signed'])->name('image.thumbnail');
});
