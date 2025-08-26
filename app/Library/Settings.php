<?php

namespace App\Library;

use Illuminate\Support\Arr;

class Settings
{
    public static function get(string $key, $default = null)
    {
        // Read from config, which merges DB values over config/settings.php defaults
        $all = config('settings', []);
        return Arr::get($all, $key, $default);
    }
}
