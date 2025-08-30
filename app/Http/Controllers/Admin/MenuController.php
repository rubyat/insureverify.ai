<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q');
        $menus = Menu::query()
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Menu/Index', [
            'menus' => $menus,
            'filters' => ['q' => $q],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Menu/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active','inactive'])],
            'items' => ['nullable', 'array'],
            'locations' => ['nullable', 'array'],
        ]);

        $menu = Menu::create($data);
        return redirect()->route('admin.menu.edit', $menu)->with('success', 'Menu created');
    }

    public function edit(Menu $menu)
    {
        return Inertia::render('Admin/Menu/Edit', [
            'menu' => $menu,
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active','inactive'])],
            'items' => ['nullable', 'array'],
            'locations' => ['nullable', 'array'],
        ]);

        $menu->update($data);
        return back()->with('success', 'Menu updated');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu deleted');
    }

    // Auxiliary endpoints
    public function contentTypes()
    {
        $types = [
            [
                'class' => \App\Models\Page::class,
                'name' => 'Pages',
                'searchable' => true,
            ],
            [
                'class' => \App\Models\Blog::class,
                'name' => 'Blogs',
                'searchable' => true,
            ],
            [
                'class' => \App\Models\BlogCategory::class,
                'name' => 'Blog Categories',
                'searchable' => true,
            ],
        ];
        return response()->json($types);
    }

    public function searchContent(Request $request)
    {
        $validated = $request->validate([
            'class' => ['required', 'string'],
            'q' => ['nullable', 'string'],
        ]);
        $class = $validated['class'];
        abort_unless(in_array($class, [\App\Models\Page::class, \App\Models\Blog::class, \App\Models\BlogCategory::class]), 422, 'Unsupported class');

        $q = $validated['q'] ?? null;
        $query = $class::query();
        // Search by title and slug when provided
        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                   ->orWhere('slug', 'like', "%{$q}%");
            });
        }
        // Return more items by default to make discovery easier
        $items = $query->latest('updated_at')->limit($q ? 20 : 50)->get();

        $mapped = $items->map(function ($item) use ($class) {
            $name = $item->title ?? $item->name ?? (string) $item->id;
            $slug = $item->slug ?? null;
            $url = $slug ? '/' . ltrim($slug, '/') : '#';
            return [
                'id' => $item->id,
                'name' => $name,
                'url' => $url,
                'item_model' => $class,
            ];
        });

        return response()->json($mapped);
    }

    public function locations()
    {
        // Could be fetched from settings later
        return response()->json([
            ['key' => 'primary', 'name' => 'Primary Navigation'],
            ['key' => 'footer', 'name' => 'Footer Navigation'],
            ['key' => 'secondary', 'name' => 'Secondary Navigation'],
        ]);
    }
}
