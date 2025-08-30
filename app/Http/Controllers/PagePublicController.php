<?php

namespace App\Http\Controllers;

use App\Library\Meta;
use App\Library\PageBuilder\TemplatePreprocessor;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PagePublicController extends Controller
{
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
        Meta::addTitle($title);
        if ($page->seo) {
            foreach ([
                'description' => $page->seo->seo_description,
                'og:title' => $title,
                'og:description' => $page->seo->seo_description,
                'og:url' => url('/' . ltrim($page->slug, '/')),
                'og:type' => 'website',
                'og:image' => $page->seo->seo_image,
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $title,
                'twitter:description' => $page->seo->seo_description,
                'twitter:image' => $page->seo->seo_image,
            ] as $k => $v) {
                if ($v) Meta::addMeta($k, $v);
            }
        }

        return Inertia::render('Page/Show', [
            'page' => $page,
            'template' => $template,
        ]);
    }
}
