<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class CallToActionBlock extends BlockBase
{
    public string $id = 'call_to_action';
    public string $name = 'Call To Action';
    public string $category = 'Other Block';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Title', 'std' => ''],
                ['id' => 'sub_title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Subtitle', 'std' => ''],
                ['id' => 'link_title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Button Text', 'std' => 'Learn more'],
                ['id' => 'link_more', 'type' => 'input', 'inputType' => 'text', 'label' => 'Button URL', 'std' => '#'],
                ['id' => 'style', 'type' => 'radios', 'label' => 'Style', 'values' => ['normal', 'style_2', 'style_3'], 'std' => 'normal'],
                ['id' => 'bg_color', 'type' => 'input', 'inputType' => 'text', 'label' => 'Background Color', 'std' => '#f7fafc'],
                ['id' => 'bg_image', 'type' => 'uploader', 'label' => 'Background Image', 'std' => null],
            ],
            'model' => [
                'title' => '',
                'sub_title' => '',
                'link_title' => 'Learn more',
                'link_more' => '#',
                'style' => 'normal',
                'bg_color' => '#f7fafc',
                'bg_image' => null,
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
