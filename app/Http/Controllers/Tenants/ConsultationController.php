<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Consultation\UpdateConsultationRequest;
use App\Http\Traits\LogsRecordDownloads;
use App\Services\Tenants\ConsultationService;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    use LogsRecordDownloads;

    protected $ConsultationService;

    public function __construct(ConsultationService $ConsultationService)
    {
        $this->ConsultationService = $ConsultationService;
    }

    public function index()
    {
        return view("tenant.consultation.index");
    }

    public function list(Request $request)
    {
        $consultations = $this->ConsultationService->all($request->term);

        if ($consultations) {
            return response()->json([
                'status' => true,
                'consultations' => $consultations
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => false
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $consultation = $this->ConsultationService->store($request->all());

        if ($consultation) {
            return response()->json([
                'status' => true,
                'consultation' => $consultation,
            ], 200);

        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function show($tenant, $id, Request $request)
    {
        $consultation = $this->ConsultationService->find($id);

        if ($consultation && $consultation->patient) {
            $this->auditRecordView($consultation->patient, $request, [
                'consultation_id' => $consultation->id,
                'route' => 'tenant.consultation.show',
            ]);
        }

        return view("tenant.consultation.show", compact('id'));
    }
    public function find($tenant, $id)
    {
        $consultation = $this->ConsultationService->find($id);

        if ($consultation) {
            return response()->json([
                'status' => true,
                'consultation' => $consultation,
            ], 200);

        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function update($tenant, $id, UpdateConsultationRequest $request)
    {
        $consultation = $this->ConsultationService->update($id, $request->all());

        if ($consultation) {
            return response()->json([
                'status' => true,
                'consultation' => $consultation,
            ], 200);

        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }
}
