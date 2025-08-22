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
                'name' => 'Starter',
                'slug' => 'starter',
                'stripe_plan_id' => 'na_starter',
                'anet_plan_id' => 'BASIC_MONTHLY',
                'price' => 9.00,
                'image_limit' => 50,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'stripe_plan_id' => 'na_pro',
                'anet_plan_id' => 'PRO_MONTHLY',
                'price' => 19.00,
                'image_limit' => 200,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'stripe_plan_id' => 'na_business',
                'anet_plan_id' => 'BUSINESS_MONTHLY',
                'price' => 49.00,
                'image_limit' => 1000,
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


