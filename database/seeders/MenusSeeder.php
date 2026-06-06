<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['id' => 1, 'label' => 'Home', 'icon' => 'bi bi-house', 'url' => '/admin/home', 'active' => 1, 'son' => null, 'order' => 1, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-11 11:22:46'],
            
            ['id' => 2, 'label' => 'Administrativo', 'icon' => 'bi bi-speedometer', 'url' => '#', 'active' => 1, 'son' => null, 'order' => 2, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-11 11:22:46'],
            ['id' => 3, 'label' => 'Usuários', 'icon' => 'bi bi-people', 'url' => '/admin/users', 'active' => 1, 'son' => 2, 'order' => null, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-03 11:11:29'],
            ['id' => 4, 'label' => 'Perfis', 'icon' => 'bi bi-person-badge', 'url' => '/admin/roles', 'active' => 1, 'son' => 2, 'order' => null, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-03 11:11:29'],
            ['id' => 5, 'label' => 'Permissões', 'icon' => 'bi bi-lock', 'url' => '/admin/permissions', 'active' => 1, 'son' => 2, 'order' => null, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-03 11:11:29'],
            ['id' => 6, 'label' => 'Menus', 'icon' => 'bi bi-compass', 'url' => '/admin/menu', 'active' => 1, 'son' => 2, 'order' => null, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-03 11:11:29'],
            ['id' => 10, 'label' => 'Sair', 'icon' => 'bi bi-box-arrow-right', 'url' => '/admin/logout', 'active' => 1, 'son' => null, 'order' => 7, 'created_at' => '2025-02-03 11:11:29', 'updated_at' => '2025-02-11 18:56:24'],

            ['id' => 11, 'label' => 'Site', 'icon' => 'bi bi-globe', 'url' => '/admin/site', 'active' => 1, 'son' => null, 'order' => 3, 'created_at' => '2025-03-04 11:47:05', 'updated_at' => '2025-03-04 11:47:05'],

            ['id' => 18, 'label' => 'CRM', 'icon' => 'bi bi-person-square', 'url' => '#', 'active' => 1, 'son' => null, 'order' => 4, 'created_at' => '2025-03-04 11:50:52', 'updated_at' => '2025-03-08 18:55:53'],
            ['id' => 19, 'label' => 'Clients', 'icon' => 'bi bi-person-vcard', 'url' => '/admin/crm/clients/', 'active' => 1, 'son' => 18, 'order' => 18, 'created_at' => '2025-03-04 11:52:31', 'updated_at' => '2025-03-04 11:52:31'],
            ['id' => 20, 'label' => 'Status', 'icon' => 'bi bi-check-circle', 'url' => '/admin/crm/status/', 'active' => 1, 'son' => 18, 'order' => 18, 'created_at' => '2025-03-04 11:52:31', 'updated_at' => '2025-03-04 11:52:31'],
            ['id' => 21, 'label' => 'Interações', 'icon' => 'bi bi-chat-left-text', 'url' => '/admin/crm/interactions/', 'active' => 1, 'son' => 18, 'order' => 18, 'created_at' => '2025-03-04 11:52:31', 'updated_at' => '2025-03-04 11:52:31'],
            
            ['id' => 22, 'label' => 'Financiero', 'icon' => 'bi bi-boxes', 'url' => '#', 'active' => 1, 'son' => null, 'order' => 5, 'created_at' => '2025-03-08 16:12:01', 'updated_at' => '2025-03-11 21:17:40'],
            ['id' => 23, 'label' => 'Planos', 'icon' => 'bi bi-card-checklist', 'url' => '/admin/financial/plan/', 'active' => 1, 'son' => 22, 'order' => 22, 'created_at' => '2025-03-08 16:17:27', 'updated_at' => '2025-03-11 21:17:31'],
            ['id' => 24, 'label' => 'Inscrições', 'icon' => 'bi bi-bookmark-plus', 'url' => '/admin/financial/subscriptions/', 'active' => 1, 'son' => 22, 'order' => 22, 'created_at' => '2025-03-08 16:17:27', 'updated_at' => '2025-03-11 21:17:33'],
            ['id' => 25, 'label' => 'Pagamentos', 'icon' => 'bi bi-cash-stack', 'url' => '/admin/financial/payments/', 'active' => 1, 'son' => 22, 'order' => 22, 'created_at' => '2025-03-08 18:55:23', 'updated_at' => '2025-03-11 21:17:39'],
        ];

        DB::table('menus')->insert($menus);
    }
}
