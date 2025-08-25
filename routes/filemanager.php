<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FileManagerController;
use App\Http\Controllers\Admin\ThumbController;

Route::middleware(['auth', 'role:admin|super-admin'])
    ->prefix('admin/api/filemanager')
    ->name('filemanager.')
    ->group(function () {
        Route::get('/list', [FileManagerController::class, 'list'])->name('list');
        Route::post('/upload', [FileManagerController::class, 'upload'])->name('upload');
        Route::post('/folder', [FileManagerController::class, 'folder'])->name('folder');
        Route::post('/delete', [FileManagerController::class, 'delete'])->name('delete');
    });

// public thumb endpoint (serves cached thumbs or generates them on the fly)
Route::get('/thumb/{size}/{path}', [ThumbController::class, 'show'])
    ->where('path', '.*')
    ->name('thumb.show');
