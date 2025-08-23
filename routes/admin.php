<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ReportsController;

// Admin routes, protected by admin role
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|super-admin'])
    ->group(function () {
        Route::resource('plans', AdminPlanController::class);
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

        // Super Admin only actions
        Route::middleware('role:super-admin')->group(function () {
            Route::post('users/{user}/make-admin', [UsersController::class, 'makeAdmin'])->name('users.makeAdmin');
            Route::post('users/{user}/remove-admin', [UsersController::class, 'removeAdmin'])->name('users.removeAdmin');
        });
    });
