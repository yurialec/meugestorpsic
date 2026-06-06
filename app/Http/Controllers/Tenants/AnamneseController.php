<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Anamnese\AnamneseStoreUpdateRequest;
use App\Models\Tenants\Patient;
use App\Services\Tenants\AnamneseService;
use Illuminate\Http\Request;

class AnamneseController extends Controller
{
    protected $AnamneseService;

    public function __construct(AnamneseService $AnamneseService)
    {
        $this->AnamneseService = $AnamneseService;
    }

    public function index($tenant, $patient_id)
    {
        return view('tenant.patient.anamnese.index', compact('patient_id'));
    }

    public function store(AnamneseStoreUpdateRequest $request)
    {
        $data = $request->all();
        $result = $this->AnamneseService->create($data);

        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Anamnese salva com sucesso.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao salvar anamnese.',
            ], 500);
        }
    }

    public function find($tenant, $patient_id)
    {
        $data = $this->AnamneseService->find($patient_id);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Nenhum resultado encontrado'
            ], 500);
        }
    }
}