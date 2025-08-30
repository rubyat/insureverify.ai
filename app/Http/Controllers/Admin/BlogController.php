<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q');
        $blogs = Blog::query()->with('category')
            ->when($q, fn($qq) => $qq->where('title', 'like', "%{$q}%")->orWhere('slug', 'like', "%{$q}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Blogs/Index', [
            'blogs' => $blogs,
            'filters' => ['q' => $q],
        ]);
    }

    public function create()
    {
        $categories = \App\Models\BlogCategory::query()
            ->orderBy('title')
            ->get(['id','title']);
        return Inertia::render('Admin/Blogs/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:blogs,slug'],
            'status' => ['required', Rule::in([0,1])],
            'content' => ['nullable', 'string'],
            'template' => ['nullable', 'array'],
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'publish_date' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'image' => ['nullable', 'string'],
            'seo' => ['array'],
            'seo.seo_title' => ['nullable', 'string', 'max:255'],
            'seo.seo_index' => ['nullable', 'boolean'],
            'seo.seo_keyword' => ['nullable', 'string'],
            'seo.seo_description' => ['nullable', 'string'],
            'seo.seo_image' => ['nullable', 'string'],
            'seo.canonical_url' => ['nullable', 'string', 'max:500'],
            'seo.meta_json' => ['nullable', 'array'],
        ], [
            'slug.unique' => 'The slug is already taken.',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $blog = Blog::create(collect($data)->except('seo')->all());
        if (!empty($data['seo'])) {
            $blog->seo()->create($data['seo']);
        }

        return redirect()->route('admin.blogs.edit', $blog)->with('success', 'Blog created');
    }

    public function edit(Blog $blog)
    {
        $blog->load('seo', 'category');
        $categories = \App\Models\BlogCategory::query()
            ->orderBy('title')
            ->get(['id','title']);
        return Inertia::render('Admin/Blogs/Edit', [
            'blog' => $blog,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blogs', 'slug')->ignore($blog->id)],
            'status' => ['required', Rule::in([0,1])],
            'content' => ['nullable', 'string'],
            'template' => ['nullable', 'array'],
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'publish_date' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'image' => ['nullable', 'string'],
            'seo' => ['array'],
            'seo.seo_title' => ['nullable', 'string', 'max:255'],
            'seo.seo_index' => ['nullable', 'boolean'],
            'seo.seo_keyword' => ['nullable', 'string'],
            'seo.seo_description' => ['nullable', 'string'],
            'seo.seo_image' => ['nullable', 'string'],
            'seo.canonical_url' => ['nullable', 'string', 'max:500'],
            'seo.meta_json' => ['nullable', 'array'],
        ], [
            'slug.unique' => 'The slug is already taken.',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $blog->update(collect($data)->except('seo')->all());
        $blog->seo()->updateOrCreate([], $data['seo'] ?? []);

        return back()->with('success', 'Blog updated');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted');
    }

    public function duplicate(Blog $blog)
    {
        // Replicate attributes
        $copy = $blog->replicate();
        $copy->title = trim(($blog->title ?? 'Untitled') . ' - copy');
        $copy->status = 0; // inactive/draft

        // Generate unique slug from new title
        $baseSlug = Str::slug($copy->title) ?: 'blog';
        $slug = $baseSlug;
        $i = 2;
        while (Blog::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }
        $copy->slug = $slug;

        $copy->push();

        // Duplicate SEO if exists
        $blog->loadMissing('seo');
        if ($blog->seo) {
            $seoData = $blog->seo->only([
                'seo_title', 'seo_index', 'seo_keyword', 'seo_description', 'seo_image', 'canonical_url', 'meta_json'
            ]);
            $copy->seo()->create($seoData);
        }

        return redirect()->route('admin.blogs.index')->with('success', 'Blog cloned');
    }
}
