<?php

namespace Database\Seeders;

use App\Models\Admin\Plan;
use App\Models\Tenants\PlanFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mensal = Plan::where('name', 'Mensal')->first();
        $anual = Plan::where('name', 'Anual')->first();

        PlanFeature::create([
            'plan_id' => $mensal->id,
            'type' => 'individual',
            'name' => 'Individual',
            'user_limit' => 1,
            'extra_price' => 29.90,
        ]);

        PlanFeature::create([
            'plan_id' => $anual->id,
            'type' => 'individual',
            'name' => 'Individual',
            'user_limit' => 1,
            'extra_price' => 319.90,
        ]);

        PlanFeature::create([
            'plan_id' => $mensal->id,
            'type' => 'clinic',
            'name' => 'Básico',
            'user_limit' => 5,
            'extra_price' => 149.90,
        ]);

        PlanFeature::create([
            'plan_id' => $anual->id,
            'type' => 'clinic',
            'name' => 'Básico',
            'user_limit' => 5,
            'extra_price' => 1590.00,
        ]);

        PlanFeature::create([
            'plan_id' => $mensal->id,
            'type' => 'clinic',
            'name' => 'Pro',
            'user_limit' => 10,
            'extra_price' => 299.00,
        ]);

        PlanFeature::create([
            'plan_id' => $anual->id,
            'type' => 'clinic',
            'name' => 'Pro',
            'user_limit' => 10,
            'extra_price' => 2990.00,
        ]);

        PlanFeature::create([
            'plan_id' => $mensal->id,
            'type' => 'clinic',
            'name' => 'Premium',
            'user_limit' => 20,
            'extra_price' => 499.90,
        ]);

        PlanFeature::create([
            'plan_id' => $anual->id,
            'type' => 'clinic',
            'name' => 'Premium',
            'user_limit' => 20,
            'extra_price' => 4990.00,
        ]);
    }
}
