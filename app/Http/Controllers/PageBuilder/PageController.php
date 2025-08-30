<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q');
        $pages = Page::query()
            ->when($q, fn($qq) => $qq->where('title', 'like', "%{$q}%")->orWhere('slug', 'like', "%{$q}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
            'filters' => ['q' => $q],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug'],
            'status' => ['required', Rule::in([0,1])],
            'is_home' => ['nullable', 'boolean'],
            'content' => ['nullable', 'string'],
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

        $data['is_home'] = (bool) ($data['is_home'] ?? false);

        $page = Page::create(collect($data)->except('seo')->all());
        // Ensure single home page
        if ($page->is_home) {
            Page::query()->where('id', '!=', $page->id)->update(['is_home' => false]);
        }
        if (!empty($data['seo'])) {
            $page->seo()->create($data['seo']);
        }

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created');
    }

    public function edit(Page $page)
    {
        $page->load('seo');
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page->id)],
            'status' => ['required', Rule::in([0,1])],
            'is_home' => ['nullable', 'boolean'],
            'content' => ['nullable', 'string'],
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

        $data['is_home'] = (bool) ($data['is_home'] ?? false);

        $page->update(collect($data)->except('seo')->all());
        // Ensure single home page
        if ($page->is_home) {
            Page::query()->where('id', '!=', $page->id)->update(['is_home' => false]);
        }
        // Use morphOne constraints; no need to pass foreign keys
        $page->seo()->updateOrCreate([], $data['seo'] ?? []);

        return back()->with('success', 'Page updated');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted');
    }

    public function duplicate(Page $page)
    {
        // Replicate attributes
        $copy = $page->replicate();
        $copy->title = trim(($page->title ?? 'Untitled') . ' - copy');
        $copy->status = 0; // inactive/draft
        $copy->is_home = false; // never copy home flag

        // Generate unique slug from new title
        $baseSlug = Str::slug($copy->title) ?: 'page';
        $slug = $baseSlug;
        $i = 2;
        while (Page::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }
        $copy->slug = $slug;

        $copy->push();

        // Duplicate SEO if exists
        $page->loadMissing('seo');
        if ($page->seo) {
            $seoData = $page->seo->only([
                'seo_title', 'seo_index', 'seo_keyword', 'seo_description', 'seo_image', 'canonical_url', 'meta_json'
            ]);
            $copy->seo()->create($seoData);
        }

        return redirect()->route('admin.pages.index')->with('success', 'Page cloned');
    }
}

