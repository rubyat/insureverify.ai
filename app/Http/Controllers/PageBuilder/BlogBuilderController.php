<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Library\PageBuilder\Registry;
use App\Library\PageBuilder\Renderer;
use App\Models\Blog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\FileManagerService;
use App\Models\Plan;

class BlogBuilderController extends Controller
{
    protected Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function editor(Blog $blog)
    {
        $blog->load('seo');
        $template = $blog->template ?: null;
        if (!$template || !is_array($template) || !isset($template['ROOT'])) {
            $template = [
                'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
            ];
        }

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

        return Inertia::render('Admin/Blogs/TemplateBuilder', [
            'page' => $blog, // reuse prop name expected by component
            'template' => $template,
            'blocksEndpoint' => route('admin.blocks.index'),
            'livePreviewEndpoint' => route('admin.blogs.live_preview', $blog->id),
            'saveEndpoint' => route('admin.blogs.template.save', $blog->id),
            'blocksThumbEndpoint' => route('admin.blocks.thumb'),
        ]);
    }

    public function save(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'template' => ['required', 'array'],
        ]);
        $blog->update(['template' => $data['template']]);

        $template = $blog->template;

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

    public function livePreview(Blog $blog)
    {
        $template = $blog->template ?: [
            'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
        ];

        /** @var FileManagerService $fm */
        $fm = app(FileManagerService::class);
        foreach ($template as $nid => &$node) {
            if (!is_array($node)) continue;
            $type = $node['type'] ?? null;
            $model = $node['model'] ?? null;
            if (!is_array($model)) continue;

            $w = $model['image_width'] ?? null;
            $h = $model['image_height'] ?? 0;

            if(isset($model['background_image']) && !empty($model['background_image'])){
                $bg = $model['background_image'] ?? null;
                if (is_string($bg) && $bg !== '' && $w !== null && (int)$w > 0) {
                    $node['model']['background_image'] = $fm->resize($bg, (int)$w, (int)$h);
                }
            }

            if(isset($model['image']) && !empty($model['image'])){
                $img = $model['image'] ?? null;
                if (is_string($img) && $img !== '' && $w !== null && (int)$w > 0) {
                    $node['model']['image'] = $fm->resize($img, (int)$w, (int)$h);
                }
            }

            if ($type === 'pricing') {
                $plans = Plan::query()
                    ->where('is_active', true)
                    ->where('visibility', 'Public')
                    ->orderBy('sort_order')
                    ->orderBy('price')
                    ->get([
                        'id', 'name', 'slug', 'price', 'image_limit', 'description',
                        'verifications_included', 'features', 'cta_label', 'cta_route', 'anet_plan_id'
                    ]);
                $node['model'] = array_merge(['plans' => $plans], $node['model'] ?? []);
            }
        }
        unset($node);

        return Inertia::render('Admin/Pages/LivePreview', [
            'page' => $blog,
            'template' => $template,
        ]);
    }
}
