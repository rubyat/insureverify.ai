<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Subscribed\PagesController;
use App\Http\Controllers\Subscribed\AuthController as MarketingAuthController;
use App\Http\Controllers\PlanController as PublicPlanController;
use App\Http\Controllers\App\PagesController as AppPagesController;
use App\Http\Controllers\App\VerificationController as AppVerificationController;
use App\Http\Controllers\Cron\SubscriptionRenewalController;

// Public marketing site routes
Route::get('/', [PagesController::class, 'home'])->name('home');
Route::get('/features', [PagesController::class, 'features'])->name('features');
Route::get('/about-us', [PagesController::class, 'about'])->name('about');
Route::get('/docs', [PagesController::class, 'docs'])->name('docs');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::post('/contact', [PagesController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy-policy', [PagesController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PagesController::class, 'terms'])->name('terms');
// Signup routes handled by Marketing AuthController
Route::get('/signup', [MarketingAuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [MarketingAuthController::class, 'signup'])->name('signup.store');
// Keep existing public plans listing; add a convenience alias
Route::get('/pricing', function () {
    return redirect()->route('plans.index');
})->name('pricing');

// Public plans listing
Route::get('/plans', [PublicPlanController::class, 'index'])->name('plans.index');
// Public plan signup/preview page
Route::get('/plan/{slug}', [PublicPlanController::class, 'show'])->name('plan.show');

// Frontend (authenticated customer) routes
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
});

// Minimal Subscription Dashboard pages (static-friendly) under /app
Route::prefix('app')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AppPagesController::class, 'dashboard'])->name('app.dashboard');
    Route::get('/upload', [AppPagesController::class, 'upload'])->name('app.upload');
    Route::get('/verification', [AppVerificationController::class, 'index'])->name('app.verification');
    Route::post('/verification/upload', [ImageController::class, 'store'])
        ->name('app.verification.upload')
        ->withoutMiddleware('verified');
    Route::post('/verification/verify', [AppVerificationController::class, 'verify'])
        ->name('app.verification.verify')
        ->withoutMiddleware('verified');
    Route::get('/library', [AppPagesController::class, 'library'])->name('app.library');
    Route::get('/usage', [AppPagesController::class, 'usage'])->name('app.usage');
    Route::get('/billing', [AppPagesController::class, 'billing'])->name('app.billing');
    Route::get('/notifications', [AppPagesController::class, 'notifications'])->name('app.notifications');
    Route::get('/support', [AppPagesController::class, 'support'])->name('app.support');
});

// Cron endpoint to process subscription renewals (token protected)
Route::match(['GET','POST'], '/cron/subscriptions/renew', [SubscriptionRenewalController::class, 'run'])
    ->name('cron.subscriptions.renew');
