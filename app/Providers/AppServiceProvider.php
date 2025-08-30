<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Menu;

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
            'logo' => config('settings.logo'),
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

        // Share menus by location: primary, footer, secondary
        Inertia::share('menus', function () {
            $locations = ['primary', 'footer', 'secondary'];
            $menus = Menu::query()
                ->where('status', 'active')
                ->whereIn('location', $locations)
                ->get(['id', 'name', 'location', 'items'])
                ->keyBy('location');

            // Ensure arrays for each expected location, even if missing
            $result = [];
            foreach ($locations as $loc) {
                $result[$loc] = (array) ($menus[$loc]->items ?? []);
            }
            return $result;
        });
    }
}
