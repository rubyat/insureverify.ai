<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Library\PageBuilder\Registry;
use App\Library\PageBuilder\Renderer;
use App\Models\Page;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\FileManagerService;

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
        if (!$template || !is_array($template) || !isset($template['ROOT'])) {
            $template = [
                'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
            ];
        }

        // Enrich template with server-generated thumbs for uploader fields
        /** @var FileManagerService $fm */
        $fm = app(FileManagerService::class);
        $reg = $this->registry;
        foreach ($template as $nid => &$node) {
            if (!is_array($node)) continue;
            $type = $node['type'] ?? null;
            $model = $node['model'] ?? null;
            if (!$type || !is_array($model)) continue;
            $block = $reg->get($type);
            if (!$block) continue;
            $opts = $block->getOptions();
            $settings = $opts['settings'] ?? [];
            foreach ($settings as $s) {
                if (($s['type'] ?? null) !== 'uploader') continue;
                $key = $s['id'] ?? null;
                if (!$key) continue;
                $val = $model[$key] ?? null;
                if (is_string($val) && $val !== '') {
                    $thumb = $fm->thumb($val, 300, 300);
                    $node['model'][$key . '_path'] = $thumb;
                    $node['model']['placeholder'] = $thumb;
                }
            }
        }
        unset($node);

        return Inertia::render('Admin/Pages/TemplateBuilder', [
            'page' => $page,
            'template' => $template,
            'blocksEndpoint' => route('admin.blocks.index'),
            'livePreviewEndpoint' => route('admin.pages.live_preview', $page->id),
            'saveEndpoint' => route('admin.pages.template.save', $page->id),
            'blocksThumbEndpoint' => route('admin.blocks.thumb'),
            'editEndpoint' => route('admin.pages.edit', $page->id),
        ]);
    }

    public function thumb(Request $request)
    {
        $data = $request->validate([
            'path' => ['required', 'string'],
            'w' => ['nullable', 'integer', 'min:1'],
            'h' => ['nullable', 'integer', 'min:1'],
        ]);
        /** @var FileManagerService $fm */
        $fm = app(FileManagerService::class);
        $w = (int)($data['w'] ?? 300);
        $h = (int)($data['h'] ?? 300);
        $thumb = $fm->thumb($data['path'], $w, $h);
        return response()->json(['thumb' => $thumb]);
    }

    public function save(Request $request, Page $page)
    {
        $data = $request->validate([
            'template' => ['required', 'array'],
        ]);
        $page->update(['template' => $data['template']]);

        $template = $page->template;

        // Preprocess: if a node has background_image and image_width, convert to thumb URL
        /** @var FileManagerService $fm */
        // $fm = app(FileManagerService::class);
        // $template = $page->template;
        // foreach ($template as $nid => &$node) {
        //     if (!is_array($node)) continue;
        //     $model = $node['model'] ?? null;
        //     if (!is_array($model)) continue;
        //     $bg = $model['background_image'] ?? null;
        //     $w = $model['image_width'] ?? null;
        //     if (is_string($bg) && $bg !== '' && $w !== null && (int)$w > 0) {
        //         $node['model']['background_image'] = $fm->resize($bg, (int)$w, 0);
        //     }
        // }


        // Enrich pricing blocks with plans for immediate client-side preview after save
        $plans = Plan::query()
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get([
                'id', 'name', 'slug', 'price', 'image_limit', 'description',
                'verifications_included', 'features', 'cta_label', 'cta_route', 'anet_plan_id'
            ]);

        foreach ($template as $nid => &$node) {
            if (!is_array($node)) continue;
            $type = $node['type'] ?? null;
            if ($type !== 'pricing') continue;
            $node['model'] = array_merge(['plans' => $plans], $node['model'] ?? []);
        }
        unset($node);

        return response()->json([
            'ok' => true,
            'saved_at' => now()->toDateTimeString(),
            'template' => $template,
        ]);
    }

    public function livePreview(Page $page)
    {
        // Ensure template exists
        $template = $page->template ?: [
            'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
        ];

        // Use shared preprocessor to keep logic DRY with frontend
        $pre = app(\App\Library\PageBuilder\TemplatePreprocessor::class);
        $template = $pre->process($template);

        // Client-side rendering only
        return Inertia::render('Admin/Pages/LivePreview', [
            'page' => $page,
            'template' => $template,
        ]);
    }
}
