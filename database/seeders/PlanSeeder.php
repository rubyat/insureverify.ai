<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'stripe_plan_id' => 'silver_monthly',
                'anet_plan_id' => null,
                'price' => 99.00,
                'image_limit' => 0,
                'verifications_included' => 8,
                'description' => 'Perfect for startups, small teams, or companies just getting started.',
                'features' => [
                    'No monthly minimum',
                    'Access to all verification features',
                    'Real-time license and insurance checks',
                    'Email and chat support',
                ],
                'cta_label' => 'Get Started',
                'cta_route' => 'plans.index',
                'sort_order' => 1,
                'visibility' => 'Public',
                'is_active' => true,
            ],
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'stripe_plan_id' => 'bronze_monthly',
                'anet_plan_id' => null,
                'price' => 199.00,
                'image_limit' => 0,
                'verifications_included' => 20,
                'description' => 'For growing car rental platforms or businesses with steady verification volume.',
                'features' => [
                    'Priority API access',
                    'Full audit trail and activity logs',
                    'Dedicated account manager',
                ],
                'cta_label' => 'Get Started',
                'cta_route' => 'plans.index',
                'sort_order' => 2,
                'visibility' => 'Public',
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'stripe_plan_id' => 'gold_monthly',
                'anet_plan_id' => null,
                'price' => 349.00,
                'image_limit' => 0,
                'verifications_included' => 40,
                'description' => 'Verify identity, license, and insurance in real-time with API integration.',
                'features' => [
                    'Identity verification',
                    'License status validation',
                    'Insurance coverage check',
                    'Real-time API response',
                    'Fraud prevention system',
                    'Basic API analytics',
                ],
                'cta_label' => 'Get Started',
                'cta_route' => 'plans.index',
                'sort_order' => 3,
                'visibility' => 'Public',
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'stripe_plan_id' => 'platinum_monthly',
                'anet_plan_id' => null,
                'price' => 499.00,
                'image_limit' => 0,
                'verifications_included' => 75,
                'description' => 'Verify identity, license, insurance, and record a phone call with AI-backed voice confirmation for added security.',
                'features' => [
                    'Identity verification',
                    'License & insurance checks',
                    'AI-enhanced voice confirmation',
                    'Call recording with timestamps',
                    'Advanced fraud prevention',
                    'Customizable verification flow',
                    'Priority API support',
                ],
                'cta_label' => 'Request Access',
                'cta_route' => 'plans.index',
                'sort_order' => 4,
                'visibility' => 'Public',
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'stripe_plan_id' => 'enterprise_custom',
                'anet_plan_id' => null,
                'price' => null, // custom pricing
                'image_limit' => 0,
                'verifications_included' => null,
                'description' => 'Custom plans for high-volume fleets, platforms, or partners who need advanced features and support.',
                'features' => [
                    'Volume-based discounts',
                    'Custom SLAs',
                    'API usage insights and reporting',
                    'Premium support and onboarding',
                ],
                'cta_label' => 'Contact us',
                'cta_route' => 'contact',
                'sort_order' => 5,
                'visibility' => 'Public',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}


