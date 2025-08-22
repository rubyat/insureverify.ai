<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\PlanController as PublicPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Marketing\PagesController;

// Marketing site
Route::get('/', [PagesController::class, 'home'])->name('home');
Route::get('/features', [PagesController::class, 'features'])->name('features');
Route::get('/about-us', [PagesController::class, 'about'])->name('about');
Route::get('/docs', [PagesController::class, 'docs'])->name('docs');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::post('/contact', [PagesController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy-policy', [PagesController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PagesController::class, 'terms'])->name('terms');
Route::get('/signup', [PagesController::class, 'signup'])->name('signup');
// Keep existing public plans listing; add a convenience alias
Route::get('/pricing', function () {
    return redirect()->route('plans.index');
})->name('pricing');

Route::get('dashboard', function () {
    $user = auth()->user();
    $images = [];
    $usage = null;
    if ($user) {
        $images = $user->images()->latest()->get()->map(fn($img) => [
            'id' => $img->id,
            'url' => asset('storage/'.$img->path),
        ]);
        $plan = $user->currentPlan();
        $sub = optional($user->subscription('default'));
        $cycleStart = $sub->last_renewed_at ?? $sub->created_at;
        $count = $user->images()->when($cycleStart, fn($q) => $q->where('created_at', '>=', $cycleStart))->count();
        if ($plan) {
            $usage = [
                'used' => $count,
                'limit' => $plan->image_limit,
            ];
        }
    }
    return Inertia::render('Dashboard', [
        'images' => $images,
        'usage' => $usage,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Admin routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::resource('plans', AdminPlanController::class);
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    });

// Public plans listing
Route::get('/plans', [PublicPlanController::class, 'index'])->name('plans.index');

// Subscription routes
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
});
