<?php

namespace App\Library\PageBuilder;

use App\Models\Plan;
use App\Services\FileManagerService;

class TemplatePreprocessor
{
    public function __construct(
        protected FileManagerService $fm
    ) {}

    /**
     * Process a template array: resize images and inject dynamic data (e.g., plans).
     *
     * @param array $template
     * @return array processed template
     */
    public function process(array $template): array
    {
        foreach ($template as $nid => &$node) {
            if (!is_array($node)) continue;
            $type = $node['type'] ?? null;
            $model = $node['model'] ?? null;
            if (!is_array($model)) continue;

            $w = $model['image_width'] ?? null;
            $h = $model['image_height'] ?? 0;

            if (isset($model['background_image']) && !empty($model['background_image'])) {
                $bg = $model['background_image'] ?? null;
                if (is_string($bg) && $bg !== '' && $w !== null && (int)$w > 0) {
                    $node['model']['background_image'] = $this->fm->resize($bg, (int)$w, (int)$h);
                }
            }

            if (isset($model['image']) && !empty($model['image'])) {
                $img = $model['image'] ?? null;
                if (is_string($img) && $img !== '' && $w !== null && (int)$w > 0) {
                    $node['model']['image'] = $this->fm->resize($img, (int)$w, (int)$h);
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

        return $template;
    }
}
