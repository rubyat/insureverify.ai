<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class TextBlock extends BlockBase
{
    public string $id = 'text';
    public string $name = 'Text';
    public string $category = 'Other Block';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'content', 'type' => 'editor', 'label' => 'Content', 'std' => ''],
                ['id' => 'class', 'type' => 'input', 'inputType' => 'text', 'label' => 'CSS Classes', 'std' => 'prose'],
                ['id' => 'padding', 'type' => 'spacing', 'label' => 'Padding', 'std' => 'py-6'],
            ],
            'model' => [
                'content' => '',
                'class' => 'prose',
                'padding' => 'py-6',
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
