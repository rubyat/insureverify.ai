<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class WhyChooseBlock extends BlockBase
{
    public string $id = 'why_choose';
    public string $name = 'Why Choose Section';
    public string $category = 'Home Page';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Heading', 'std' => 'Why Choose InsureVerifyAI?'],
                ['id' => 'subtitle', 'type' => 'input', 'inputType' => 'text', 'label' => 'Subheading', 'std' => 'Smarter Verifications. Safer Rentals'],
                ['id' => 'highlight_amount', 'type' => 'input', 'inputType' => 'text', 'label' => 'Highlight Amount', 'std' => '$300,000'],
                ['id' => 'highlight_label', 'type' => 'input', 'inputType' => 'text', 'label' => 'Highlight Label', 'std' => 'Fraud-Related Theft Coverage'],
                [
                    'id' => 'features',
                    'type' => 'listItem',
                    'label' => 'Features',
                    'settings' => [
                        ['id' => 'icon', 'type' => 'input', 'inputType' => 'text', 'label' => 'Icon (FontAwesome class)', 'std' => 'fa-solid fa-bolt'],
                        ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Title', 'std' => ''],
                        ['id' => 'text', 'type' => 'input', 'inputType' => 'text', 'label' => 'Description', 'std' => ''],
                    ],
                ],
            ],
            'model' => [
                'title' => 'Why Choose InsureVerifyAI?',
                'subtitle' => 'Smarter Verifications. Safer Rentals',
                'highlight_amount' => '$300,000',
                'highlight_label' => 'Fraud-Related Theft Coverage',
                'features' => [
                    [
                        'icon' => 'fa-solid fa-bolt',
                        'title' => 'Identity & License Verification (Powered by AI + Biometric Tech)',
                        'text' => 'We verify each renter using the same system trusted by CLEAR at airports. It scans their government-issued ID and facial biometrics to ensure the license is real and belongs to the person renting. This step also includes $300,000 liability coverage in case of any verification errors — providing you with maximum protection.',
                    ],
                    [
                        'icon' => 'fa-solid fa-code',
                        'title' => 'Bonus Add-On: Recorded Insurance Confirmation (Highly Recommended)',
                        'text' => 'As an optional add-on, this feature records the insurance verification process, providing you with a copy of the recording that confirms the renter’s coverage is active and applies to the rental vehicle’s actual cash value. It’s one of the most powerful tools available for backing up your coverage in the event of a claim.',
                    ],
                    [
                        'icon' => 'fa-solid fa-shield',
                        'title' => 'Real-Time Insurance Verification',
                        'text' => 'Our AI automatically reads the declarations page (deck page) of a customer’s insurance policy and checks whether the policy is active and valid. No more manual uploads or follow-ups — it’s verified instantly.',
                    ],
                    [
                        'icon' => 'fa-solid fa-chart-column',
                        'title' => 'Scalable Growth',
                        'text' => 'From small rental agencies to enterprise-level fleets, our API grows with you. Handle more bookings without hiring more staff.',
                    ],
                ],
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only; server content not required
        return '';
    }
}
