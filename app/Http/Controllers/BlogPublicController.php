<?php

namespace App\Http\Controllers;

use App\Library\Meta;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Library\Settings;
use App\Library\PageBuilder\TemplatePreprocessor;

class BlogPublicController extends Controller
{
    public function index(Request $request, ?string $category = null): Response
    {

        // Ensure $q is a plain string (Request::string returns Stringable)
        $q = (string) $request->string('q');

        $categories = BlogCategory::query()
            ->where('status', 1)
            ->orderBy('title')
            ->get(['id','title','slug']);

        $blogs = Blog::query()
            ->with('category')
            ->where('status', 1)
            // Apply search only when non-empty and group OR conditions properly
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->when($category, function ($query) use ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->latest('publish_date')
            ->paginate(9)
            ->withQueryString();

        // SEO meta
        $title = 'Blog' . ($category ? ' - ' . str_replace('-', ' ', ucfirst($category)) : '');
        Meta::addTitle($title);
        Meta::addMeta('description', 'Insights, updates, and guides from InsureVerify AI.');

        return Inertia::render('Blog/Index', [
            'blogs' => $blogs,
            'categories' => $categories,
            'activeCategory' => $category,
            'filters' => ['q' => $q],
        ]);
    }

    public function show__(string $slug): Response
    {
        $blog = Blog::query()
            ->with('category', 'seo')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // SEO meta (prefer blog SEO if exists)
        $title = $blog->seo->seo_title ?? $blog->title;
        Meta::addTitle($title);
        if ($blog->seo) {
            foreach ([
                'description' => $blog->seo->seo_description,
                'og:title' => $title,
                'og:description' => $blog->seo->seo_description,
                'og:url' => url('/blog/'.$blog->slug),
                'og:type' => 'article',
                'og:image' => $blog->seo->seo_image,
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $title,
                'twitter:description' => $blog->seo->seo_description,
                'twitter:image' => $blog->seo->seo_image,
            ] as $k => $v) {
                if ($v) Meta::addMeta($k, $v);
            }
        }

        return Inertia::render('Blog/Show', [
            'blog' => $blog,
        ]);
    }


    public function show(Request $request, string $slug): Response
    {
        $blog = Blog::query()
            ->with('category', 'seo')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        if (! $blog) {
            abort(404);
        }

        $template = $blog->template ?: [
            'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
        ];

        /** @var TemplatePreprocessor $pre */
        $pre = app(TemplatePreprocessor::class);
        $template = $pre->process($template);

        // SEO meta
        $title = $blog->seo->seo_title ?? $blog->title ?? ucfirst($blog->slug);
        $description = $blog->seo->seo_description ?? null;
        $keywords = $blog->seo->seo_keyword ?? null;
        $canonical = url('/' . ltrim($blog->slug, '/'));
        $image = $blog->seo->seo_image ?? null;
        Meta::addTitle($title);
        if ($blog->seo) {
            foreach ([
                'description' => $blog->seo->seo_description,
                'og:title' => $title,
                'og:description' => $blog->seo->seo_description,
                'og:url' => $canonical,
                'og:type' => 'website',
                'og:image' => $blog->seo->seo_image,
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $title,
                'twitter:description' => $blog->seo->seo_description,
                'twitter:image' => $blog->seo->seo_image,
            ] as $k => $v) {
                if ($v) Meta::addMeta($k, $v);
            }

            // Dynamic Schema.org JSON-LD from seo.meta_json.schema
            $schemaCfg = $blog->seo->meta_json['schema'] ?? null;
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

        return Inertia::render('Blog/Show', [
            'blog' => $blog,
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
