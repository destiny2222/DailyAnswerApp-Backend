<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            // [
            //     'name' => 'Free Trial',
            //     'price' => 0.00,
            //     'interval' => 'quarterly',
            //     'plan_id' => 'plan_free_trial',
            //     'slug' => 'free-trial',
            //     'features' => [
            //         'Basic devotionals access',
            //         'Limited memory verses',
            //         'Valid for 3 months',
            //     ],
            // ],
            [
                'name' => 'Standard Quarterly',
                'price' => 9.99,
                'interval' => 'quarterly',
                'plan_id' => 'plan_standard_quarterly',
                'slug' => 'standard-quarterly',
                'features' => [
                    'Unlimited devotionals access',
                    'All memory verses',
                    'Prayer notes',
                    'Personal notes',
                    'Daily reminders',
                    'Renews every 3 months',
                ],
            ],
            // [
            //     'name' => 'Premium Quarterly',
            //     'price' => 39.99,
            //     'interval' => 'quarterly',
            //     'plan_id' => 'plan_premium_quarterly',
            //     'slug' => 'premium-quarterly',
            //     'features' => [
            //         'Unlimited devotionals access',
            //         'All memory verses',
            //         'Prayer notes',
            //         'Personal notes',
            //         'Daily reminders',
            //         'Priority support',
            //         'Offline access',
            //         'Ad-free experience',
            //         'Renews every 3 months',
            //     ],
            // ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['plan_id' => $plan['plan_id']],
                $plan
            );
        }
    }
}
