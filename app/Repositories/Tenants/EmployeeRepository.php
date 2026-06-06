<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\EmployeeInterface;
use App\Models\Tenants\Employee;
use App\Models\Tenants\EmployeeConfig;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Log;

class EmployeeRepository implements EmployeeInterface
{
    protected $employee;
    protected $employeeConfig;

    public function __construct(Employee $employee, EmployeeConfig $employeeConfig)
    {
        $this->employee = $employee;
        $this->employeeConfig = $employeeConfig;
    }

    public function all($term)
    {
        try {
            return $this->employee
                ->where('tenant_id', session('tenant_id'))
                ->when($term, function ($query) use ($term) {
                    return $query->where(function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%')
                            ->orWhere('email', 'like', '%' . $term . '%')
                            ->orWhere('crp', 'like', '%' . $term . '%');
                    });
                })
                ->paginate(10);
        } catch (Exception $err) {
            Log::error('Erro ao listar funcionários', [$err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        try {
            return $this->employee->find($id);
        } catch (Exception $err) {
            Log::error('Erro ao localizar usuários', [$err->getMessage()]);
            return false;
        }
    }

    public function create(array $data)
    {
        try {
            $employee = $this->employee->create($data);

            $employeeConfig = $this->employeeConfig;
            $employeeConfig->employee_id = $employee->id;
            $employeeConfig->save();

            return $employee;
        } catch (Exception $e) {
            Log::error('Erro ao criar paciente', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function update($id, array $data)
    {
        try {
            $employee = $this->employee->find($id);
            $employee->fill($data);
            $employee->save();

            return $employee;
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function disable($id)
    {
        try {
            $employee = $this->employee->find($id);
            $employee->active = $employee->active ? 0 : 1;
            $employee->save();
            return $employee;
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }
}