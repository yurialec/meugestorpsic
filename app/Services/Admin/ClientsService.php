<?php

namespace App\Services\Admin;

use App\Enums\RolesEnum;
use App\Helpers\Utils;
use App\Http\Traits\TraitPlan;
use App\Models\Admin\Plan;
use App\Models\Admin\Roles;
use App\Repositories\Admin\ClientsRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ClientsService
{
    protected $ClientsRepository;

    public function __construct(ClientsRepository $ClientsRepository)
    {
        $this->ClientsRepository = $ClientsRepository;
    }

    public function all()
    {
        return $this->ClientsRepository->all();
    }

    public function create($data)
    {
        $tenantData = [
            'domain' => Utils::sanitizeDomain($data['domain']),
            'type' => $data['type'],
            'user_limit' => Utils::userLimit($data['type'], $data['user_limit']),
        ];

        $clientData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'crp' => Utils::sanitizeInteger($data['crp']),
            'password' => Hash::make($data['password']),
            'cpf' => Utils::sanitizeInteger($data['cpf']),
            'phone' => Utils::sanitizeInteger($data['phone']),
        ];

        $plan = Plan::where('name', Plan::TESTE_GRATIS)->first();
        $now = Carbon::now();
        $endDate = $now->copy()->addDays(Plan::TRAIL);

        $subcriptionData = [
            'plan_id' => $plan->id,
            'status' => 'trial',
            'started_at' => $now,
            'current_period_start' => $now,
            'current_period_end' =>  $endDate,
            'payment_method'       => null,
            'amount_paid'          => 0,
        ];

        return $this->ClientsRepository->create($clientData, $tenantData, $subcriptionData);
    }

    public function find($id)
    {
        return $this->ClientsRepository->find($id);
    }

    public function update($id, $data)
    {
        $clientData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'cpf' => Utils::sanitizeInteger($data['cpf']),
            'role_id' => Roles::CLIENTE,
        ];

        $subcriptionData = [
            'plan_id' => $data['plan_id'],
            'status' => 'Active',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => '',
            'payment_gateway_subscription_id' => null,
        ];

        return $this->ClientsRepository->update($id, $clientData, $subcriptionData);
    }

    public function delete($id)
    {
        return $this->ClientsRepository->delete($id);
    }
}
