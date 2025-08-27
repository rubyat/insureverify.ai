<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class FaqBlock extends BlockBase
{
    public string $id = 'faq';
    public string $name = 'FAQ';
    public string $category = 'Other Block';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Title', 'std' => ''],
                ['id' => 'desc', 'type' => 'input', 'inputType' => 'text', 'label' => 'Description', 'std' => ''],
                [
                    'id' => 'items',
                    'type' => 'listItem',
                    'label' => 'FAQ Items',
                    'title_field' => 'question',
                    'settings' => [
                        ['id' => 'question', 'type' => 'input', 'inputType' => 'text', 'label' => 'Question'],
                        ['id' => 'answer', 'type' => 'editor', 'label' => 'Answer'],
                    ],
                ],
            ],
            'model' => [
                'title' => '',
                'desc' => '',
                'items' => [],
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering
        return '';
    }
}
