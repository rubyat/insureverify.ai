<?php

namespace App\Library\PageBuilder;

class Renderer
{
    public function __construct(protected Registry $registry)
    {
    }

    public function render(array $template): string
    {
        if (!isset($template['ROOT'])) {
            return '';
        }
        return $this->renderNode('ROOT', $template);
    }

    protected function renderNode(string $id, array $tpl): string
    {
        $node = $tpl[$id] ?? null;
        if (!$node) return '';
        $type = $node['type'] ?? '';
        $block = $this->registry->get($type);
        if (!$block) return '';

        $html = $block->content($node['model'] ?? []);

        // If container, render children and inject into placeholder
        $childrenHtml = '';
        foreach (($node['nodes'] ?? []) as $childId) {
            $childrenHtml .= $this->renderNode($childId, $tpl);
        }
        if (str_contains($html, '{{children}}')) {
            $html = str_replace('{{children}}', $childrenHtml, $html);
        } else {
            $html .= $childrenHtml;
        }
        return $html;
    }
}
