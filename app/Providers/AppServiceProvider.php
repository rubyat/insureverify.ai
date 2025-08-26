<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share('settings', [
            'footer_description' => config('settings.footer_description'),
            'contact_email' => config('settings.contact_email'),
            'business_hours' => config('settings.business_hours'),
            'copyright' => config('settings.copyright'),
            'social' => [
                'facebook' => config('settings.facebook'),
                'twitter' => config('settings.twitter'),
                'instagram' => config('settings.instagram'),
                'linkedin' => config('settings.linkedin'),
                'youtube' => config('settings.youtube'),
            ],
        ]);
    }
}
