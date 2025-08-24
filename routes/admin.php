<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SubscribersController;
use App\Http\Controllers\Admin\BillingController as AdminBillingController;
use App\Http\Controllers\Admin\UsageController as AdminUsageController;
use App\Http\Controllers\Admin\PaymentsController as AdminPaymentsController;
use App\Http\Controllers\Admin\DashboardController;

// Admin routes, protected by admin role
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|super-admin'])
    ->group(function () {
        // Admin dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('plans', AdminPlanController::class);
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

        // Subscribers management
        Route::get('subscribers', [SubscribersController::class, 'index'])->name('subscribers.index');
        Route::get('subscribers/{user}', [SubscribersController::class, 'show'])->name('subscribers.show');

        // Billing (invoices)
        Route::get('billing', [AdminBillingController::class, 'index'])->name('billing.index');
        Route::get('billing/{invoice}', [AdminBillingController::class, 'show'])->name('billing.show');

        // Usage
        Route::get('usage', [AdminUsageController::class, 'index'])->name('usage.index');

        // Payments
        Route::get('payments', [AdminPaymentsController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [AdminPaymentsController::class, 'show'])->name('payments.show');

        // Super Admin only actions
        Route::middleware('role:super-admin')->group(function () {
            Route::post('users/{user}/make-admin', [UsersController::class, 'makeAdmin'])->name('users.makeAdmin');
            Route::post('users/{user}/remove-admin', [UsersController::class, 'removeAdmin'])->name('users.removeAdmin');
        });
    });
