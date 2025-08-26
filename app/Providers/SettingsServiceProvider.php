<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load settings from DB (cache to reduce queries)
        $dbSettings = Cache::remember('app_settings_config', 300, function () {
            return Setting::query()
                ->get(['key', 'value'])
                ->mapWithKeys(function ($row) {
                    $value = $row->value;
                    $decoded = json_decode($value, true);
                    $final = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
                    return [$row->key => $final];
                })
                ->toArray();
        });

        // Merge with config defaults from config/settings.php
        $defaults = config('settings', []);
        config(['settings' => array_replace($defaults, $dbSettings)]);
    }
}
