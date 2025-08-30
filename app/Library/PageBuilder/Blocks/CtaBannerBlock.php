<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class CtaBannerBlock extends BlockBase
{
    public string $id = 'cta_banner';
    public string $name = 'CTA Banner';
    public string $category = 'Sections';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Title', 'std' => 'Ready to Approve More Renters in Seconds?'],
                ['id' => 'subtitle', 'type' => 'input', 'inputType' => 'text', 'label' => 'Subtitle', 'std' => 'Start verifying driving licenses and insurance instantly.'],
                ['id' => 'button_text', 'type' => 'input', 'inputType' => 'text', 'label' => 'Button Text', 'std' => 'Get Started'],
                ['id' => 'button_url', 'type' => 'input', 'inputType' => 'text', 'label' => 'Button URL', 'std' => '/signup'],
                ['id' => 'background_color', 'type' => 'input', 'inputType' => 'text', 'label' => 'Background Color', 'std' => '#000000'],
                ['id' => 'text_color', 'type' => 'input', 'inputType' => 'text', 'label' => 'Text Color', 'std' => '#ffffff'],
            ],
            'model' => [
                'title' => 'Ready to Approve More Renters in Seconds?',
                'subtitle' => 'Start verifying driving licenses and insurance instantly.',
                'button_text' => 'Get Started',
                'button_url' => '/signup',
                'background_color' => '#000000',
                'text_color' => '#ffffff',
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
