<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Crm\Status\CreateStatusRequest;
use App\Http\Requests\Admin\Crm\Status\UpdateStatusRequest;
use App\Services\Admin\CrmStatusService;
use Illuminate\Http\Request;

class CrmStatusController extends Controller
{
    protected $CrmStatusService;

    public function __construct(CrmStatusService $CrmStatusService)
    {
        $this->CrmStatusService = $CrmStatusService;
    }

    public function index()
    {
        return view('admin.crm.status.index');
    }

    public function list(Request $request)
    {
        $statuses = $this->CrmStatusService->all();

        if ($statuses) {
            return response()->json([
                'status' => true,
                'statuses' => $statuses
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
        return view('admin.crm.status.create');
    }

    public function store(CreateStatusRequest $request)
    {
        $sts = $this->CrmStatusService->create($request->all());

        if ($sts) {
            return response()->json([
                true,
            ], 200);
        } else {
            return response()->json([
                false
            ], 500);
        }
    }

    public function delete($id)
    {
        $sts = $this->CrmStatusService->delete($id);

        if ($sts) {
            return response()->json([
                true,
            ], 200);
        } else {
            return response()->json([
                false,
            ], 500);
        }
    }

    public function edit($id)
    {
        return view('admin.crm.status.edit', compact('id'));
    }

    public function find($id)
    {
        $sts = $this->CrmStatusService->find($id);
        if ($sts) {
            return response()->json([
                'status' => true,
                'sts' => $sts,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao localizar Status'
            ], 500);
        }
    }

    public function update($id, UpdateStatusRequest $request)
    {
        $sts = $this->CrmStatusService->update($id, $request->all());

        if ($sts) {
            return response()->json([
                true
            ], 200);
        } else {
            return response()->json([
                false,
            ], 400);
        }
    }
}