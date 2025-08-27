<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Library\PageBuilder\Registry;
use App\Library\PageBuilder\Renderer;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuilderController extends Controller
{
    protected Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function blocks()
    {
        return response()->json($this->registry->catalog());
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'block' => ['required', 'string'],
            'model' => ['array'],
        ]);

        $block = $this->registry->get($data['block']);
        if (!$block) {
            return response()->json(['error' => 'Block not found'], 404);
        }
        $html = $block->preview($data['model'] ?? []);
        return response()->json(['preview' => $html]);
    }

    public function render(Request $request)
    {
        $data = $request->validate([
            'template' => ['required', 'array'],
        ]);
        $renderer = new Renderer($this->registry);
        $html = $renderer->render($data['template']);
        return response()->json(['preview' => $html]);
    }

    public function editor(Page $page)
    {
        $page->load('seo');
        // Ensure template has a ROOT
        $template = $page->template ?: null;
        if (!$template) {
            $template = [
                'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
            ];
        }

        return Inertia::render('Admin/Pages/TemplateBuilder', [
            'page' => $page,
            'template' => $template,
            'blocksEndpoint' => route('admin.blocks.index'),
            'livePreviewEndpoint' => route('admin.pages.live_preview', $page->id),
            'saveEndpoint' => route('admin.pages.template.save', $page->id),
        ]);
    }

    public function save(Request $request, Page $page)
    {
        $data = $request->validate([
            'template' => ['required', 'array'],
        ]);
        $page->update(['template' => $data['template']]);
        return response()->json([
            'ok' => true,
            'saved_at' => now()->toDateTimeString(),
            'template' => $page->template,
        ]);
    }

    public function livePreview(Page $page)
    {
        // Ensure template exists
        $template = $page->template ?: [
            'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
        ];

        // Client-side rendering only
        return Inertia::render('Admin/Pages/LivePreview', [
            'page' => $page,
            'template' => $template,
        ]);
    }
}
