<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class ContentSectionBlock extends BlockBase
{
    public string $id = 'content_section';
    public string $name = 'Content Section';
    public string $category = 'Common';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                [
                    'id' => 'layout',
                    'type' => 'radios',
                    'label' => 'Layout',
                    'values' => ['image_left', 'image_right', 'text_full'],
                    'std' => 'image_right',
                ],
                [
                    'id' => 'title',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Title',
                    'std' => 'About Us',
                ],
                [
                    'id' => 'content',
                    'type' => 'editor',
                    'label' => 'Content (HTML)',
                    'std' => '<p>Your content here</p>',
                ],
                [
                    'id' => 'image',
                    'type' => 'uploader',
                    'label' => 'Image',
                    'std' => null,
                    'placeholder' => '/storage/placeholder.png',
                ],
                [
                    'id' => 'image_alt',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Image Alt Text',
                    'std' => '',
                ],
                [
                    'id' => 'image_width',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Image Width (px)',
                    'std' => '800',
                ],
                [
                    'id' => 'image_height',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Image Height (px, 0 = auto)',
                    'std' => '0',
                ],
                [
                    'id' => 'cta_text',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'CTA Text',
                    'std' => '',
                ],
                [
                    'id' => 'cta_url',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'CTA URL',
                    'std' => '',
                ],
            ],
            'model' => [
                'layout' => 'image_right',
                'title' => 'About Us',
                'content' => '<p>Your content here</p>',
                'image' => null,
                'image_alt' => '',
                'image_width' => 800,
                'image_height' => 0,
                'cta_text' => '',
                'cta_url' => '',
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
