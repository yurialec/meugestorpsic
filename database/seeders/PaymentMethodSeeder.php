<?php

namespace Database\Seeders;

use App\Models\Admin\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            "cash" => 'Dinheiro',
            "pix" => 'PIX',
            "credit_card" => 'Cartão',
            "health_insurance" => 'Convênio',
            "tank_transfer" => 'Transferência',
            "free" => 'Livre',
            "pending" => 'Pendente',
        ];

        foreach ($values as $name => $label) {
            PaymentMethod::firstOrCreate(
                ['name' => $name],
                ['label' => $label]
            );
        }
    }
}
