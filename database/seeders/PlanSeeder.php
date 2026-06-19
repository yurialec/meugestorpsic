<?php

namespace Database\Seeders;

use App\Models\Admin\Plan;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Teste Grátis',
            'price' => 0.00,
            'description' => 'Teste Grátis por 10 dias',
            'billing_cycle' => 'monthly',
            'payment_gateway_plan_code' => null,
        ]);

        Plan::create([
            'name' => 'Mensal',
            'price' => 29.90,
            'description' => 'Plano Mensal',
            'billing_cycle' => 'monthly',
            'payment_gateway_plan_code' => null,
        ]);

        Plan::create([
            'name' => 'Anual',
            'price' => 319.90,
            'description' => 'Plano Anual',
            'billing_cycle' => 'annual',
            'payment_gateway_plan_code' => null,
        ]);
    }
}
