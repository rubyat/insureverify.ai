<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use App\Services\FileManagerService;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Resolve services here (avoid constructor injection on providers)
        $fm = app(FileManagerService::class);
        // Load settings from DB (cache to reduce queries)
        $dbSettings = Cache::remember('app_settings_config', 300, function () use ($fm) {
            return Setting::query()
                ->get(['key', 'value'])
                ->mapWithKeys(function ($row) use ($fm) {
                    $value = $row->value;
                    $decoded = json_decode($value, true);
                    $final = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
                    // If key is 'logo', transform to absolute asset path under images/
                    if ($row->key === 'logo' && is_string($final) && $final !== '' && $final !== null) {
                        $final = $fm->raw($final);
                    }

                    if ($row->key === 'favicon' && is_string($final) && $final !== '' && $final !== null) {
                        $final = $fm->thumb($final, 64, 64);
                    }

                    return [$row->key => $final];
                })
                ->toArray();
        });

        // Merge with config defaults from config/settings.php
        $defaults = config('settings', []);
        config(['settings' => array_replace($defaults, $dbSettings)]);
    }
}
