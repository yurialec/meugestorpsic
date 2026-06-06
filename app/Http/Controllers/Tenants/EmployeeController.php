<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\StoreEmployeeRequest;
use App\Models\Tenants\Employee;
use App\Services\Tenants\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends TenantController
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        return view('tenant.employee.index');
    }

    public function list(Request $request)
    {
        $employee = $this->employeeService->all($request->term);

        if ($employee) {
            return response()->json([
                'status' => true,
                'employee' => $employee
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function create()
    {
        return view('tenant.employee.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = $this->employeeService->store($request->all());

        if ($employee) {
            return response()->json([
                'status' => true,
                'employee' => $employee
            ], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao cadastrar funcionário.',
                'status' => 500
            ]);
        }
    }

    public function disable($tenant, $id)
    {
        $employee = $this->employeeService->disable($id);

        if ($employee) {
            return response()->json([
                'status' => true,
                'employee' => $employee
            ], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao alterar status do usuário.',
                'status' => 500
            ]);
        }
    }

    public function edit($tenant, $id)
    {
        return view('tenant.employee.edit', compact('id'));
    }

    public function find($tenant, $id)
    {
        $employee = $this->employeeService->find($id);

        if ($employee) {
            return response()->json([
                'status' => true,
                'employee' => $employee
            ], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao alterar status do usuário.',
                'status' => 500
            ]);
        }
    }

    public function update($tenant, $id, Request $request)
    {
        $employee = $this->employeeService->update($id, $request->all());

        if ($employee) {
            return response()->json([
                'status' => true,
                'employee' => $employee
            ], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao alterar status do usuário.',
                'status' => 500
            ]);
        }
    }
}
