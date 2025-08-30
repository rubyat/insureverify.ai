<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class PartnersBlock extends BlockBase
{
    public string $id = 'partners';
    public string $name = 'Partners Section';
    public string $category = 'Sections';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'settings' => [
                ['id' => 'eyebrow', 'type' => 'input', 'inputType' => 'text', 'label' => 'Eyebrow', 'std' => 'PARTNER. CLIENT. COMMUNITY.'],
                ['id' => 'title', 'type' => 'input', 'inputType' => 'text', 'label' => 'Heading', 'std' => 'We Work With the Best Partners'],
                ['id' => 'text', 'type' => 'textarea', 'label' => 'Description', 'std' => 'We collaborate with leading companies, organizations, and communities that share our values. These partnerships help us deliver better, faster, and more innovative solutions for everyone we serve.'],
                ['id' => 'cta_label', 'type' => 'input', 'inputType' => 'text', 'label' => 'CTA Label', 'std' => 'Read More'],
                ['id' => 'cta_url', 'type' => 'input', 'inputType' => 'text', 'label' => 'CTA URL', 'std' => '/about-us'],
                [
                    'id' => 'partners',
                    'type' => 'listItem',
                    'label' => 'Partners',
                    'settings' => [
                        ['id' => 'name', 'type' => 'input', 'inputType' => 'text', 'label' => 'Name', 'std' => ''],
                        ['id' => 'logo', 'type' => 'input', 'inputType' => 'text', 'label' => 'Logo URL', 'std' => ''],
                        ['id' => 'url', 'type' => 'input', 'inputType' => 'text', 'label' => 'Link URL', 'std' => 'https://example.com'],
                    ],
                ],
            ],
            'model' => [
                'eyebrow' => 'PARTNER. CLIENT. COMMUNITY.',
                'title' => 'We Work With the Best Partners',
                'text' => 'We collaborate with leading companies, organizations, and communities that share our values. These partnerships help us deliver better, faster, and more innovative solutions for everyone we serve.',
                'cta_label' => 'Read More',
                'cta_url' => '/about-us',
                'partners' => [
                    [ 'name' => 'RentFYV', 'logo' => '/images/partner/RentFIV.jpg', 'url' => 'https://fyvco.com/' ],
                    [ 'name' => 'PANDA EXOTIC CAR RENTALS', 'logo' => '/images/partner/panda.jpg', 'url' => 'https://pandaexoticcarrental.com/' ],
                    [ 'name' => 'Premier Auto Rental', 'logo' => '/images/partner/PremierAutoRental.jpg', 'url' => 'https://premierautolosangeles.com/' ],
                    [ 'name' => 'Studinovski', 'logo' => '/images/partner/Studinovski.jpg', 'url' => 'https://studinovskiexotics.com/' ],
                    [ 'name' => 'FYV EXOTIC CAR RENTAL', 'logo' => '/images/partner/FYVEXOTICCARRENTAL.jpg', 'url' => 'https://fyvexoticcarrental.com/' ],
                    [ 'name' => 'Gather', 'logo' => '/images/partner/Gather.jpg', 'url' => 'https://gather.technology/' ],
                ],
            ],
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
