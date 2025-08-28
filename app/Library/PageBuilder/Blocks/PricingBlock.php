<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class PricingBlock extends BlockBase
{
    public string $id = 'pricing';
    public string $name = 'Pricing';
    public string $category = 'Home Page';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                [
                    'id' => 'title',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Title',
                    'std' => 'Pricing',
                ],
                [
                    'id' => 'subtitle',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Subtitle',
                    'std' => 'Simple, transparent pricing that scales with your business',
                ],
            ],
            'model' => [
                'title' => 'Pricing',
                'subtitle' => 'Simple, transparent pricing that scales with your business',
                // 'plans' will be injected automatically by the controller for preview/render
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
