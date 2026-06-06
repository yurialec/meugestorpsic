<?php

namespace App\Services\Tenants;

use App\Helpers\Utils;
use App\Repositories\Tenants\TenantRepository;
use Illuminate\Support\Facades\Hash;

class TenantService
{
    protected $TenantRepository;

    public function __construct(TenantRepository $TenantRepository)
    {
        $this->TenantRepository = $TenantRepository;
    }

    public function all()
    {
        return $this->TenantRepository->all();
    }

    public function viewProfile()
    {
        return $this->TenantRepository->viewProfile();
    }

    public function updateProfile($id, $data): object|null
    {
        $clientData = [
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => isset($data['phone']) ? Utils::sanitizeInteger($data['phone']) : null,
            'function' => $data['function'] ?? null,
        ];

        if (!empty($data['password'])) {
            $clientData['password'] = Hash::make($data['password']);
        }

        return $this->TenantRepository->updateProfile($id, $clientData);
    }

    public function getTenantData()
    {
        return $this->TenantRepository->getTenantData();
    }

    public function birthdaysOfTheMonth()
    {
        return $this->TenantRepository->birthdaysOfTheMonth();
    }
}