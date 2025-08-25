<?php

use Illuminate\Support\Facades\Route;

// File Manager API routes (Laravel + Inertia + Vue)
// These mirror the OpenCart endpoints: list, upload, folder create, delete
// Prefix: /fm

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('fm')
    ->name('fm.')
    ->group(function () {
        Route::get('list', [\App\Http\Controllers\FileManagerController::class, 'index'])->name('list');
        Route::post('upload', [\App\Http\Controllers\FileManagerController::class, 'upload'])->name('upload');
        Route::post('folder', [\App\Http\Controllers\FileManagerController::class, 'folder'])->name('folder');
        Route::post('delete', [\App\Http\Controllers\FileManagerController::class, 'delete'])->name('delete');
    });
