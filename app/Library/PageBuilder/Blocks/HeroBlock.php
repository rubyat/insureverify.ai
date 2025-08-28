<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class HeroBlock extends BlockBase
{
    public string $id = 'hero';
    public string $name = 'Hero Section';
    public string $category = 'Sections';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                [
                    'id' => 'background_image',
                    'type' => 'uploader',
                    'label' => 'Background Image',
                    'std' => null,
                    'placeholder' => '/images/hero-image.jpg',
                ],
                [
                    'id' => 'overlay_opacity',
                    'type' => 'input',
                    'inputType' => 'number',
                    'label' => 'Overlay Opacity (0-1)',
                    'std' => '0.4'
                ],
                [
                    'id' => 'title',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Title',
                    'std' => ''
                ],
                [
                    'id' => 'subtitle',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Subtitle',
                    'std' => ''
                ],
                [
                    'id' => 'primary_text',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Primary Button Text',
                    'std' => 'Request API Access'
                ],
                [
                    'id' => 'primary_url',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Primary Button URL',
                    'std' => '/signup'
                ],
                [
                    'id' => 'secondary_text',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Secondary Button Text',
                    'std' => 'See Pricing'
                ],
                [
                    'id' => 'secondary_url',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Secondary Button URL',
                    'std' => '/plans'
                ],
            ],
            'model' => [
                'background_image' => null,
                'overlay_opacity' => '0.4',
                'title' => '',
                'subtitle' => '',
                'primary_text' => 'Request API Access',
                'primary_url' => '/signup',
                'secondary_text' => 'See Pricing',
                'secondary_url' => '/plans',
                'image_width' => 1920,
                'image_height' => 0,
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering
        return '';
    }
}
