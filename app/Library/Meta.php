<?php

namespace App\Library;

use App\Library\Settings;

class Meta
{
    protected static $meta = [];
    protected static $title = '';

    public static function addMeta($name, $content)
    {
        static::$meta[$name] = $content;
    }

    public static function addTitle($content)
    {
        static::$title = $content;
    }

    public static function render()
    {
        $html = '';

        // Use saved site title from settings if available
        $defaultTitle = Settings::get('site_title', 'InsureVerifyAI – License & Insurance Verification');
        $title = (static::$title) ? static::$title : $defaultTitle;

        $html .= '<title inertia>' . $title . '</title>' . PHP_EOL;
        foreach (static::$meta as $name => $content) {
            if (str_starts_with($name, 'og:')) {
                $html .= '<meta property="' . $name . '" content="' . $content . '" />' . PHP_EOL;
            } else {
                $html .= '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
            }
        }
        // Reset after rendering to prevent leakage between requests
        static::$meta = [];
        static::$title = '';
        return $html;
    }
}
