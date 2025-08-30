<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class FeaturesBlock extends BlockBase
{
    public string $id = 'features';
    public string $name = 'Features Section';
    public string $category = 'Sections';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Heading', 'std' => 'All the Features You Need'],
                ['id' => 'subtitle', 'type' => 'input', 'inputType' => 'text', 'label' => 'Subheading', 'std' => 'From performance to security and support.'],
                [
                    'id' => 'items',
                    'type' => 'listItem',
                    'label' => 'Feature Items',
                    'settings' => [
                        ['id' => 'icon', 'type' => 'input', 'inputType' => 'text', 'label' => 'FontAwesome Icon Class', 'std' => 'fa-solid fa-bolt'],
                        ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Title', 'std' => ''],
                        ['id' => 'text', 'type' => 'input', 'inputType' => 'text', 'label' => 'Description', 'std' => ''],
                    ],
                ],
                ['id' => 'cta_label', 'type' => 'input', 'inputType' => 'text', 'label' => 'CTA Label', 'std' => 'Request API Access'],
                ['id' => 'cta_url', 'type' => 'input', 'inputType' => 'text', 'label' => 'CTA URL', 'std' => '/signup'],
            ],
            'model' => [
                'title' => 'All the Features You Need',
                'subtitle' => 'From performance to security and support.',
                'items' => [
                    [
                        'icon' => 'fa-solid fa-bolt',
                        'title' => 'Fast Verification',
                        'text' => 'Instant license and insurance checks with high accuracy.',
                    ],
                    [
                        'icon' => 'fa-solid fa-code',
                        'title' => 'Developer Friendly',
                        'text' => 'Clean REST APIs, sandbox, and test data.',
                    ],
                    [
                        'icon' => 'fa-solid fa-shield-halved',
                        'title' => 'Secure & Encrypted',
                        'text' => 'Encryption in transit and at rest. Principle of least privilege.',
                    ],
                    [
                        'icon' => 'fa-solid fa-server',
                        'title' => 'Scalable Infrastructure',
                        'text' => 'Scale from startups to enterprise fleets.',
                    ],
                ],
                'cta_label' => 'Request API Access',
                'cta_url' => '/signup',
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
