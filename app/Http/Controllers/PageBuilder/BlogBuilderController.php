<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Library\Meta;
use App\Library\PageBuilder\Registry;
use App\Library\PageBuilder\TemplatePreprocessor;
use App\Library\Settings;
use App\Models\Blog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
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
            'editEndpoint' => route('admin.blogs.edit', $blog->id),
        ]);
    }

    public function save(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'template' => ['required', 'array'],
        ]);
        $blog->update(['template' => $data['template']]);

        $template = $blog->template;

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

    public function livePreview(Blog $blog): Response
    {
        $blog->load('category', 'seo');

        $template = $blog->template ?: [
            'ROOT' => ['type' => 'root', 'nodes' => [], 'version' => '1.1'],
        ];

        /** @var TemplatePreprocessor $pre */
        $pre = app(TemplatePreprocessor::class);
        $template = $pre->process($template);

        // SEO meta
        $title = $blog->seo->seo_title ?? $blog->title ?? ucfirst($blog->slug);
        $description = $blog->seo->seo_description ?? null;
        $keywords = $blog->seo->seo_keyword ?? null;
        $canonical = url('/' . ltrim($blog->slug, '/'));
        $image = $blog->seo->seo_image ?? null;

        Meta::addTitle($title);
        if ($blog->seo) {
            foreach ([
                'description' => $blog->seo->seo_description,
                'og:title' => $title,
                'og:description' => $blog->seo->seo_description,
                'og:url' => $canonical,
                'og:type' => 'website',
                'og:image' => $blog->seo->seo_image,
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $title,
                'twitter:description' => $blog->seo->seo_description,
                'twitter:image' => $blog->seo->seo_image,
            ] as $k => $v) {
                if ($v) Meta::addMeta($k, $v);
            }
        }

        $relatedBlogs = Blog::query()
            ->where('status', 1)
            ->where('id', '!=', $blog->id)
            ->when($blog->category_id, function ($query) use ($blog) {
                $query->where('category_id', $blog->category_id);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get(['id', 'title', 'slug', 'image']);

        /** @var FileManagerService $fm */
        $fm = app(FileManagerService::class);
        $blogBanner = $fm->resize($blog->seo->seo_image, 1080, 0);

        return Inertia::render('Blog/Show', [
            'blog' => $blog,
            'blogBanner' => $blogBanner,
            'template' => $template,
            'relatedBlogs' => $relatedBlogs,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords,
                'canonical' => $canonical,
                'image' => $image,
            ],
        ]);
    }
}
