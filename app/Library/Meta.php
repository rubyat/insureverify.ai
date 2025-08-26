<?php

namespace App\Library;

use App\Library\Settings;

class Meta
{
    protected static $meta = [];
    protected static $title = '';
    protected static $schemas = [];

    public static function addMeta($name, $content)
    {
        static::$meta[$name] = $content;
    }

    public static function addTitle($content)
    {
        static::$title = $content;
    }

    /**
     * Queue a JSON-LD schema (as an associative array) to be rendered in the head.
     */
    public static function addSchema(array $schema): void
    {
        static::$schemas[] = $schema;
    }

    /**
     * Helper to add a default Organization schema using dynamic settings when available.
     */
    public static function addDefaultOrganizationSchema(): void
    {
        $siteName = Settings::get('site_title', config('app.name', 'InsureVerify AI'));
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $logo = Settings::get('site_logo_url', url('/favicon.png'));
        $supportEmail = Settings::get('support_email', 'support@insureverify.ai');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $baseUrl . '/',
            'logo' => $logo,
            'description' => Settings::get('site_tagline', 'Verify renters’ driving licenses and insurance in seconds. Reduce risk, save time, and boost ROI with our scalable API solution.'),
            'foundingDate' => Settings::get('founding_date', '2025'),
            'founder' => [
                '@type' => 'Person',
                'name' => Settings::get('founder_name', 'InsureVerify AI Team'),
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'email' => $supportEmail,
                'availableLanguage' => ['English'],
            ],
            'sameAs' => array_values(array_filter([
                Settings::get('social_instagram', 'http://Instagram.com/insureverify.ai'),
            ])),
            'knowsAbout' => [
                'AI insurance verification',
                'driver’s license verification API',
                'renters insurance verification',
                'automated eligibility checks',
                'scalable insurance API solutions',
            ],
        ];

        static::addSchema($schema);
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
        // Render any queued JSON-LD schemas
        foreach (static::$schemas as $schema) {
            $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $html .= '<script type="application/ld+json">' . PHP_EOL . $json . PHP_EOL . '</script>' . PHP_EOL;
        }
        // Reset after rendering to prevent leakage between requests
        static::$meta = [];
        static::$title = '';
        static::$schemas = [];
        return $html;
    }
}
