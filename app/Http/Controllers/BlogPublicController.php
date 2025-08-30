<?php

namespace App\Http\Controllers;

use App\Library\Meta;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogPublicController extends Controller
{
    public function index(Request $request, ?string $category = null): Response
    {
        $q = $request->string('q');

        $categories = BlogCategory::query()
            ->where('status', 1)
            ->orderBy('title')
            ->get(['id','title','slug']);

        $blogs = Blog::query()
            ->with('category')
            ->where('status', 1)
            ->when($q, fn($qq) => $qq->where('title', 'like', "%{$q}%")->orWhere('slug', 'like', "%{$q}%"))
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

    public function show(string $slug): Response
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
}
