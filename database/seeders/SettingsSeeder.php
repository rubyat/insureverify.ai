<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // Header
            ['label' => 'Logo', 'code' => 'header', 'key' => 'logo', 'value' => null, 'sort_order' => 1, 'type' => 'file', 'meta' => json_encode(['accept' => 'image/*', 'directory' => 'uploads/settings'])],
            ['label' => 'Favicon', 'code' => 'header', 'key' => 'favicon', 'value' => null, 'sort_order' => 2, 'type' => 'file', 'meta' => json_encode(['accept' => 'image/*', 'directory' => 'uploads/settings'])],
            ['label' => 'Site title', 'code' => 'header', 'key' => 'site_title', 'value' => 'InsureVerifyAI – License & Insurance Verification', 'sort_order' => 1, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'Site title'])],
            ['label' => 'Site description', 'code' => 'header', 'key' => 'site_description', 'value' => 'License & Insurance Verification', 'sort_order' => 2, 'type' => 'textarea', 'meta' => json_encode(['placeholder' => 'Description'])],
            ['label' => 'Site keywords', 'code' => 'header', 'key' => 'site_keywords', 'value' => 'InsureVerifyAI, License & Insurance Verification', 'sort_order' => 3, 'type' => 'textarea', 'meta' => json_encode(['placeholder' => 'Keywords'])],
            // header script
            ['label' => 'Header script', 'code' => 'header', 'key' => 'header_script', 'value' => '', 'sort_order' => 4, 'type' => 'textarea', 'meta' => json_encode(['placeholder' => 'Header script'])],
            // body script
            ['label' => 'Body script', 'code' => 'header', 'key' => 'body_script', 'value' => '', 'sort_order' => 5, 'type' => 'textarea', 'meta' => json_encode(['placeholder' => 'Body script'])],



            //['label' => 'Timezone', 'code' => 'general', 'key' => 'timezone', 'value' => 'UTC', 'sort_order' => 3, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'e.g., UTC'])],

            // Appearance
            // ['label' => 'Logo (Light)', 'code' => 'appearance', 'key' => 'logo_light', 'value' => null, 'sort_order' => 1, 'type' => 'file', 'meta' => json_encode(['accept' => 'image/*', 'directory' => 'uploads/settings'])],
            // ['label' => 'Logo (Dark)', 'code' => 'appearance', 'key' => 'logo_dark', 'value' => null, 'sort_order' => 2, 'type' => 'file', 'meta' => json_encode(['accept' => 'image/*', 'directory' => 'uploads/settings'])],

            // system | light | dark (used by app to determine theme; app.blade may also set this server-side)
            // ['label' => 'Theme', 'code' => 'appearance', 'key' => 'appearance', 'value' => 'system', 'sort_order' => 3, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'system | light | dark'])],

            // Footer
            ['label' => 'Footer description', 'code' => 'footer', 'key' => 'footer_description', 'value' => 'InsureVerifyAI helps car rental companies and mobility platforms instantly verify driving licenses and insurance. Our secure API reduces fraud, saves time, and speeds up renter approvals.', 'sort_order' => 2, 'type' => 'textarea', 'meta' => json_encode(['placeholder' => 'Footer description'])],
            ['label' => 'Contact email', 'code' => 'footer', 'key' => 'contact_email', 'value' => 'support@insureverify.ai', 'sort_order' => 3, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'support@example.com'])],
            ['label' => 'Business hours', 'code' => 'footer', 'key' => 'business_hours', 'value' => 'Monday to Friday, 9 AM – 6 PM (EST)', 'sort_order' => 4, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'e.g., Mon–Fri, 9–6 (EST)'])],
            // Copyright
            ['label' => 'Copyright', 'code' => 'footer', 'key' => 'copyright', 'value' => '© 2025 InsureVerifyAI. All rights reserved.', 'sort_order' => 5, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'Copyright'])],
            // footer script
            ['label' => 'Footer script', 'code' => 'footer', 'key' => 'footer_script', 'value' => '', 'sort_order' => 5, 'type' => 'textarea', 'meta' => json_encode(['placeholder' => 'Footer script'])],

            // social links
            ['label' => 'Facebook', 'code' => 'social', 'key' => 'facebook', 'value' => '', 'sort_order' => 1, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'Facebook'])],
            ['label' => 'Twitter', 'code' => 'social', 'key' => 'twitter', 'value' => '', 'sort_order' => 2, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'Twitter'])],
            ['label' => 'Instagram', 'code' => 'social', 'key' => 'instagram', 'value' => '', 'sort_order' => 3, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'Instagram'])],
            ['label' => 'LinkedIn', 'code' => 'social', 'key' => 'linkedin', 'value' => '', 'sort_order' => 4, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'LinkedIn'])],
            ['label' => 'YouTube', 'code' => 'social', 'key' => 'youtube', 'value' => '', 'sort_order' => 5, 'type' => 'input', 'meta' => json_encode(['placeholder' => 'YouTube'])],


        ];

        foreach ($rows as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key']],
                [
                    'label' => $row['label'],
                    'code' => $row['code'],
                    'value' => $row['value'],
                    'sort_order' => $row['sort_order'],
                    'type' => $row['type'],
                    'meta' => $row['meta'] ? json_decode($row['meta'], true) : null,
                ]
            );
        }
    }
}
