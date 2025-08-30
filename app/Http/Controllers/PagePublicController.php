<?php

namespace App\Http\Controllers;

use App\Library\Meta;
use App\Library\Settings;
use App\Library\PageBuilder\TemplatePreprocessor;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PagePublicController extends Controller
{
    public function home(Request $request): Response
    {
        $homeId = (int) (config('settings.home_page') ?? 0);
        if ($homeId > 0) {
            $page = Page::query()->with('seo')->where('id', $homeId)->where('status', 1)->first();
            if ($page) {
                return $this->show($request, $page->slug);
            }
        }
        abort(404);
    }

    public function show(Request $request, string $slug): Response
    {
        $page = Page::query()
            ->with('seo')
            ->where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (! $page) {
            abort(404);
        }

        $template = $page->template ?: [
            'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
        ];

        /** @var TemplatePreprocessor $pre */
        $pre = app(TemplatePreprocessor::class);
        $template = $pre->process($template);

        // SEO meta
        $title = $page->seo->seo_title ?? $page->title ?? ucfirst($page->slug);
        $description = $page->seo->seo_description ?? null;
        $keywords = $page->seo->seo_keyword ?? null;
        $canonical = url('/' . ltrim($page->slug, '/'));
        $image = $page->seo->seo_image ?? null;
        Meta::addTitle($title);
        if ($page->seo) {
            foreach ([
                'description' => $page->seo->seo_description,
                'og:title' => $title,
                'og:description' => $page->seo->seo_description,
                'og:url' => $canonical,
                'og:type' => 'website',
                'og:image' => $page->seo->seo_image,
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $title,
                'twitter:description' => $page->seo->seo_description,
                'twitter:image' => $page->seo->seo_image,
            ] as $k => $v) {
                if ($v) Meta::addMeta($k, $v);
            }

            // Dynamic Schema.org JSON-LD from seo.meta_json.schema
            $schemaCfg = $page->seo->meta_json['schema'] ?? null;
            if (is_array($schemaCfg) && !empty($schemaCfg['enabled'])) {
                $baseUrl = rtrim(config('app.url', url('/')), '/');
                $siteName = Settings::get('site_title', config('app.name', 'InsureVerify AI'));
                $logo = Settings::get('site_logo_url', url('/favicon.png'));

                $mainEntity = $schemaCfg['mainEntity'] ?? [];
                $offers = $mainEntity['offers'] ?? [];
                $publisher = [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'url' => $baseUrl . '/',
                    'logo' => $logo,
                ];

                $schema = [
                    '@context' => 'https://schema.org',
                    '@type' => $schemaCfg['@type'] ?? 'WebPage',
                    'url' => $canonical,
                    'name' => $schemaCfg['name'] ?? $title,
                    'description' => $schemaCfg['description'] ?? ($page->seo->seo_description ?? null),
                    'mainEntity' => array_filter([
                        '@type' => $mainEntity['@type'] ?? 'SoftwareApplication',
                        'name' => $mainEntity['name'] ?? $siteName,
                        'operatingSystem' => $mainEntity['operatingSystem'] ?? 'Cloud-based',
                        'applicationCategory' => $mainEntity['applicationCategory'] ?? null,
                        'offers' => array_filter([
                            '@type' => 'Offer',
                            'url' => $canonical,
                            'price' => $offers['price'] ?? null,
                            'priceCurrency' => $offers['priceCurrency'] ?? null,
                        ]),
                        'featureList' => $mainEntity['featureList'] ?? null,
                        'description' => $mainEntity['description'] ?? null,
                        'url' => $baseUrl . '/',
                        'publisher' => $publisher,
                    ]),
                ];

                Meta::addSchema($schema);
            }
        }

        return Inertia::render('Page/Show', [
            'page' => $page,
            'template' => $template,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords,
                'canonical' => $canonical,
                'image' => $image,
            ],
        ]);
    }
}
