<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Page;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Response as HttpResponse;

class SitemapController extends Controller
{
    public function index(): HttpResponse
    {
        $urls = [];

        // Static marketing routes
        $staticRoutes = [
            route('home'),
            route('features'),
            route('about'),
            route('docs'),
            route('contact'),
            route('privacy'),
            route('terms'),
            route('faq'),
            route('plans.index'),
            route('blog.index'),
        ];
        foreach ($staticRoutes as $u) {
            $urls[] = [
                'loc' => $u,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Public CMS pages (status=1)
        $pages = Page::query()
            ->where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);
        foreach ($pages as $p) {
            $urls[] = [
                'loc' => url('/' . ltrim($p->slug, '/')),
                'lastmod' => optional($p->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Blog categories (status=1)
        $cats = BlogCategory::query()->where('status', 1)->get(['slug', 'updated_at']);
        foreach ($cats as $c) {
            $urls[] = [
                'loc' => route('blog.category', ['category' => $c->slug]),
                'lastmod' => optional($c->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        // Blog posts (status=1)
        $blogs = Blog::query()
            ->where('status', 1)
            ->orderBy('publish_date', 'desc')
            ->get(['slug', 'publish_date', 'updated_at']);
        foreach ($blogs as $b) {
            $last = $b->updated_at ?: $b->publish_date;
            $urls[] = [
                'loc' => route('blog.show', ['slug' => $b->slug]),
                'lastmod' => optional($last)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.9',
            ];
        }

        // Public, active plans
        $plans = Plan::query()
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);
        foreach ($plans as $plan) {
            $urls[] = [
                'loc' => route('plan.show', ['slug' => $plan->slug]),
                'lastmod' => optional($plan->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        // Build XML
        $xml = $this->buildXml($urls);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * @param array<int, array{loc:string, lastmod?:string|null, changefreq?:string|null, priority?:string|null}> $urls
     */
    protected function buildXml(array $urls): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . e($u['loc']) . '</loc>';
            if (!empty($u['lastmod'])) {
                $xml[] = '    <lastmod>' . e($u['lastmod']) . '</lastmod>';
            }
            if (!empty($u['changefreq'])) {
                $xml[] = '    <changefreq>' . e($u['changefreq']) . '</changefreq>';
            }
            if (!empty($u['priority'])) {
                $xml[] = '    <priority>' . e($u['priority']) . '</priority>';
            }
            $xml[] = '  </url>';
        }
        $xml[] = '</urlset>';
        return implode("\n", $xml);
    }
}
