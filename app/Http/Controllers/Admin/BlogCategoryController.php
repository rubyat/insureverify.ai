<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q');
        $categories = BlogCategory::query()
            ->when($q, fn($qq) => $qq->where('title', 'like', "%{$q}%")->orWhere('slug', 'like', "%{$q}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/BlogCategories/Index', [
            'categories' => $categories,
            'filters' => ['q' => $q],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/BlogCategories/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:blog_categories,slug'],
            'status' => ['required', Rule::in([0,1])],
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

        $category = BlogCategory::create(collect($data)->except('seo')->all());
        if (!empty($data['seo'])) {
            $category->seo()->create($data['seo']);
        }

        return redirect()->route('admin.blog-categories.edit', $category)->with('success', 'Category created');
    }

    public function edit(BlogCategory $blog_category)
    {
        $blog_category->load('seo');
        return Inertia::render('Admin/BlogCategories/Edit', [
            'category' => $blog_category,
        ]);
    }

    public function update(Request $request, BlogCategory $blog_category)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_categories', 'slug')->ignore($blog_category->id)],
            'status' => ['required', Rule::in([0,1])],
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

        $blog_category->update(collect($data)->except('seo')->all());
        $blog_category->seo()->updateOrCreate([], $data['seo'] ?? []);

        return back()->with('success', 'Category updated');
    }

    public function destroy(BlogCategory $blog_category)
    {
        $blog_category->delete();
        return redirect()->route('admin.blog-categories.index')->with('success', 'Category deleted');
    }
}
