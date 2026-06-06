<?php

namespace App\Services\Tenants;

use App\Helpers\Utils;
use App\Repositories\Tenants\EmployeeRepository;
use Hash;

class EmployeeService
{
    protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function all($term)
    {
        return $this->employeeRepository->all($term);
    }

    public function store(array $data)
    {
        $employeeData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'crp' => Utils::sanitizeInteger($data['crp']),
            'password' => Hash::make($data['password']),
            'cpf' => Utils::sanitizeInteger($data['cpf']),
            'function' => $data['function'],
            'phone' => Utils::sanitizeInteger($data['phone']),
            'tenant_id' => session('tenant_id'),
        ];

        return $this->employeeRepository->create($employeeData);
    }

    public function find($id)
    {
        return $this->employeeRepository->find($id);
    }

    public function update($id, $data)
    {
        $employeeData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'crp' => Utils::sanitizeInteger($data['crp']),
            'cpf' => Utils::sanitizeInteger($data['cpf']),
            'function' => $data['function'],
            'phone' => Utils::sanitizeInteger($data['phone']),
            'tenant_id' => session('tenant_id'),
            'active' => $data['active'],
        ];

        if (isset($data['password']) && !empty($data['password'])) {
            $employeeData['password'] = Hash::make($data['password']);
        }

        return $this->employeeRepository->update($id, $employeeData);
    }

    public function disable($id)
    {
        return $this->employeeRepository->disable($id);
    }
}