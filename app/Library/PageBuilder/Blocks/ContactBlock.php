<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class ContactBlock extends BlockBase
{
    public string $id = 'contact';
    public string $name = 'Contact';
    public string $category = 'Common Sections';

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
                    'std' => 'Contact Us',
                ],
                [
                    'id' => 'subtitle',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Subtitle',
                    'std' => 'Send us a message and we’ll get back within 1–2 business days.',
                ],
                [
                    'id' => 'show_form',
                    'type' => 'switch',
                    'label' => 'Show Form',
                    'std' => true,
                ],
                [
                    'id' => 'success_text',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Success Message',
                    'std' => "Thanks! Your message has been queued. We'll get back within 1–2 business days.",
                ],
                [
                    'id' => 'contact_email',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Contact Email',
                    'std' => 'support@example.com',
                ],
                [
                    'id' => 'contact_phone',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Contact Phone',
                    'std' => '+1 555 123 4567',
                ],
                [
                    'id' => 'contact_address',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => 'Contact Address',
                    'std' => '',
                ],
            ],
            'model' => [
                'title' => 'Contact Us',
                'subtitle' => 'Send us a message and we’ll get back within 1–2 business days.',
                'show_form' => true,
                'success_text' => "Thanks! Your message has been queued. We'll get back within 1–2 business days.",
                'contact_email' => 'support@example.com',
                'contact_phone' => '+1 555 123 4567',
                'contact_address' => '',
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
