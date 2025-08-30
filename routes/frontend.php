<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Subscribed\PagesController;
use App\Http\Controllers\Subscribed\AuthController as MarketingAuthController;
use App\Http\Controllers\PlanController as PublicPlanController;
use App\Http\Controllers\App\PagesController as AppPagesController;
use App\Http\Controllers\App\BillingController as AppBillingController;
use App\Http\Controllers\App\UpgradeController as AppUpgradeController;
use App\Http\Controllers\App\VerificationController as AppVerificationController;
use App\Http\Controllers\Cron\SubscriptionRenewalController;
use App\Http\Controllers\BlogPublicController;
use App\Http\Controllers\PagePublicController;
use App\Http\Controllers\SitemapController;

// Public marketing site routes
Route::get('/', [PagesController::class, 'home'])->name('home');
Route::get('/features', [PagesController::class, 'features'])->name('features');
Route::get('/about-us', [PagesController::class, 'about'])->name('about');
Route::get('/docs', [PagesController::class, 'docs'])->name('docs');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::post('/contact', [PagesController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy-policy', [PagesController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PagesController::class, 'terms'])->name('terms');
Route::get('/faq', [PagesController::class, 'faq'])->name('faq');
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

// Public Blog
Route::get('/blog', [BlogPublicController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category}', [BlogPublicController::class, 'index'])->name('blog.category');
Route::get('/blog/{slug}', [BlogPublicController::class, 'show'])->name('blog.show');

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
    // Billing pages and card management
    Route::get('/billing', [AppBillingController::class, 'index'])->name('app.billing');
    Route::post('/billing/cards', [AppBillingController::class, 'storeCard'])->name('app.billing.cards.store');
    Route::post('/billing/cards/{card}/default', [AppBillingController::class, 'makeDefault'])->name('app.billing.cards.default');
    Route::delete('/billing/cards/{card}', [AppBillingController::class, 'destroyCard'])->name('app.billing.cards.destroy');
    Route::get('/notifications', [AppPagesController::class, 'notifications'])->name('app.notifications');
    Route::get('/support', [AppPagesController::class, 'support'])->name('app.support');
    Route::get('/upgrade', [AppUpgradeController::class, 'index'])->name('app.upgrade');
    Route::post('/upgrade', [AppUpgradeController::class, 'switch'])->name('app.upgrade.switch');
});

// Cron endpoint to process subscription renewals (token protected)
Route::match(['GET','POST'], '/cron/subscriptions/renew', [SubscriptionRenewalController::class, 'run'])
    ->name('cron.subscriptions.renew');

// SEO: Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Public CMS Pages (must be at the very end to avoid conflicts)
Route::get('/{slug}', [PagePublicController::class, 'show'])
    ->where('slug', '^[a-z0-9-]+$')
    ->name('page.show');
