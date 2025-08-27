<?php

namespace App\Library\PageBuilder;

class Registry
{
    /** @var array<string, BlockBase> */
    protected array $blocks = [];

    public function __construct()
    {
        // Register core blocks
        $this->register(new \App\Library\PageBuilder\Blocks\RootBlock());
        $this->register(new \App\Library\PageBuilder\Blocks\TextBlock());
        $this->register(new \App\Library\PageBuilder\Blocks\CallToActionBlock());
        $this->register(new \App\Library\PageBuilder\Blocks\FaqBlock());
    }

    public function register(BlockBase $block): void
    {
        $this->blocks[$block->id] = $block;
    }

    /** @return array<string, BlockBase> */
    public function all(): array
    {
        return $this->blocks;
    }

    public function get(string $id): ?BlockBase
    {
        return $this->blocks[$id] ?? null;
    }

    public function catalog(): array
    {
        // Flatten to front-end friendly grouped structure
        $groups = [];
        foreach ($this->blocks as $id => $block) {
            $opt = $block->getOptions();
            $category = $opt['category'] ?? ($block->category ?? 'Other Block');
            $groups[$category] ??= [
                'name' => $category,
                'open' => true,
                'items' => [],
            ];
            $groups[$category]['items'][] = array_merge([
                'id' => $id,
                'name' => $opt['name'] ?? $block->name,
            ], $opt);
        }
        return array_values($groups);
    }
}
