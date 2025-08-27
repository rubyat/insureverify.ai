<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

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
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug'],
            'status' => ['required', Rule::in([0,1])],
            'short_desc' => ['nullable', 'string'],
            'image_id' => ['nullable', 'integer'],
            'custom_logo' => ['nullable', 'integer'],
            'header_style' => ['nullable', 'string', 'max:100'],
            'show_template' => ['boolean'],
            'content' => ['nullable', 'string'],
            'seo' => ['array'],
            'seo.seo_title' => ['nullable', 'string', 'max:255'],
            'seo.seo_description' => ['nullable', 'string'],
            'seo.seo_image_id' => ['nullable', 'integer'],
            'seo.canonical_url' => ['nullable', 'string', 'max:500'],
        ]);

        $page = Page::create(collect($data)->except('seo')->all());
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
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page->id)],
            'status' => ['required', Rule::in([0,1])],
            'short_desc' => ['nullable', 'string'],
            'image_id' => ['nullable', 'integer'],
            'custom_logo' => ['nullable', 'integer'],
            'header_style' => ['nullable', 'string', 'max:100'],
            'show_template' => ['boolean'],
            'content' => ['nullable', 'string'],
            'seo' => ['array'],
            'seo.seo_title' => ['nullable', 'string', 'max:255'],
            'seo.seo_description' => ['nullable', 'string'],
            'seo.seo_image_id' => ['nullable', 'integer'],
            'seo.canonical_url' => ['nullable', 'string', 'max:500'],
        ]);

        $page->update(collect($data)->except('seo')->all());
        $page->seo()->updateOrCreate(['page_id' => $page->id], $data['seo'] ?? []);

        return back()->with('success', 'Page updated');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted');
    }
}
