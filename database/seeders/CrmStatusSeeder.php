<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrmStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Definindo os dados dos status
        $statuses = [
            [
                'id' => 1,
                'name' => 'Período de Teste',
                'color' => '#808080',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Interesse',
                'color' => '#FF5733',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => 'Ligação',
                'color' => '#3498DB',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => 'Contrato Fechado',
                'color' => '#2ECC71',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => 'Cadastro de dados',
                'color' => '#F1C40F',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' => 'Implantação',
                'color' => '#8E44AD',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'name' => 'Pago',
                'color' => '#1ABC9C',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'name' => 'Contato Futuro',
                'color' => '#FFC300',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'name' => 'Contato Perdido',
                'color' => '#E74C3C',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 10,
                'name' => 'Reconquista',
                'color' => '#9B59B6',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
        ];

        DB::table('crm_statuses')->insert($statuses);
    }
}
