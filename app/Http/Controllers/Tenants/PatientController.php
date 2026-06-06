<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Patient\StorePatientRequest;
use App\Http\Requests\Tenant\Patient\UpdatePatientRequest;
use App\Http\Traits\LogsRecordDownloads;
use App\Models\Admin\Clients;
use App\Models\Tenants\Anamnese;
use App\Models\Tenants\Patient;
use App\Models\Tenants\Tenant;
use App\Services\Tenants\PatientService;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Log;
use Storage;

class PatientController extends Controller
{
    use LogsRecordDownloads;

    protected $PatientService;

    public function __construct(PatientService $PatientService)
    {
        $this->PatientService = $PatientService;
    }

    public function index()
    {
        return view('tenant.patient.index');
    }

    public function list(Request $request)
    {
        $patients = $this->PatientService->all($request->term);

        if ($patients) {
            return response()->json([
                'status' => true,
                'patients' => $patients
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
        return view('tenant.patient.create');
    }

    public function store(StorePatientRequest $request)
    {
        $patient = $this->PatientService->create($request->validated());

        if ($patient) {
            return response()->json([
                'status' => true,
                'patient' => $patient,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar usuário'
            ], 500);
        }
    }

    public function disable(Request $request)
    {
        $patient = $request->input('patient');
        $patientId = is_array($patient) ? ($patient['id'] ?? null) : $patient;

        if (!$patientId) {
            return response()->json([
                'status' => false,
                'message' => 'Paciente invÃ¡lido'
            ], 422);
        }

        $client = $this->PatientService->disable($patientId);

        if ($client) {
            return response()->json([
                'status' => true,
                'message' => 'Paciente excluio com sucesso',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao excluir Paciente'
            ], 500);
        }
    }

    public function edit($tenant, $id)
    {
        return view('tenant.patient.edit', compact('id'));
    }

    public function getPatientById($tenant, $id)
    {
        $patientById = $this->PatientService->getPatientById($id);
        if ($patientById) {
            return response()->json([
                'status' => true,
                'patientById' => $patientById,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao excluir Paciente'
            ], 500);
        }
    }

    public function update($tenant, $id, UpdatePatientRequest $request)
    {
        $patient = $this->PatientService->update($id, $request->all());

        if ($patient) {
            return response()->json([
                'status' => true,
                'patient' => $patient,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar paciente'
            ], 500);
        }
    }

    public function pdf($tenant, $patient_id, Request $request)
    {
        try {
            $patient = $this->patientQueryForCurrentUser()->with('anamnese')->findOrFail($patient_id);
            $anamnese = $patient;
            if (!isset($anamnese->anamnese)) {
                throw new Exception('Anamnese não encontrada.');
            }

            $patient = $this->patientQueryForCurrentUser()->with([
                'anamnese.attitudeWithTheInterviewer',
                'anamnese.awarenessOfCurrentIllness',
                'anamnese.contents',
                'anamnese.denialOfSelf',
                'anamnese.diagnosticHypothesis',
                'anamnese.expansionOfTheSelf',
                'anamnese.familyHistory',
                'anamnese.guidance',
                'anamnese.historyOfPresentIllness',
                'anamnese.language',
                'anamnese.mood',
                'anamnese.notes',
                'anamnese.personalHistory',
                'anamnese.psychicExam',
                'anamnese.sensePerception',
                'anamnese.thought',
                'anamnese.treatment',
                'consultations' => fn($query) => $this->scopeConsultationsToCurrentUser($query)
            ])->findOrFail($patient_id);

            $client = $this->currentProfessionalData();

            $tenant = Tenant::find(session('tenant_id'));

            $psychologistLogo = null;

            if ($tenant && $tenant->logo) {
                $imagePath = storage_path('app/public/' . $tenant->logo);
                if (file_exists($imagePath) && is_readable($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $psychologistLogo = 'data:image/' . $imageType . ';base64,' . $imageData;
                }
            }

            $pdf = Pdf::loadView('tenant.pdfs.patient_report', compact('patient', 'psychologistLogo', 'client'));
            $this->auditRecordDownload($patient, $request, [
                'record_version' => 'anamnese_pdf',
                'route' => 'tenant.medicalRecord.pdf',
            ]);

            return $pdf->download('anamnese-paciente-' . $patient->id . '.pdf');
        } catch (\Throwable $th) {
            Log::error('Erro ao gerar pdf anamnese', [$th->getMessage()]);
            throw new Exception($th->getMessage());
        }
    }

    public function pdfProntuario($tenant, $patient_id, Request $request)
    {
        try {
            $patient = $this->patientQueryForCurrentUser()
                ->with([
                    'employee:id,name,crp,phone,email',
                    'consultations' => function ($query) {
                        $query->select(
                            'id',
                            'patient_id',
                            'scheduled_at',
                            'started_at',
                            'ended_at',
                            'objectives',
                            'content_worked',
                            'clinical_observations',
                            'interventions',
                            'planning',
                            'homework',
                            'emotional_state',
                            'engagement_level',
                            'insights',
                            'status',
                            'location'
                        );

                        $this->scopeConsultationsToCurrentUser($query);

                        $query->orderBy('scheduled_at');
                    },
                    'anamnese:id,patient_id,service_frequency',
                    'anamnese.historyOfPresentIllness:id,anamnese_id,beginning_of_the_pathology,frequency,intensity,previous_treatments,medications',
                    'anamnese.personalHistory:id,anamnese_id,childhood,routine,addiction,hobbies,work',
                    'anamnese.familyHistory:id,anamnese_id,parents,siblings,partner,children,home',
                    'anamnese.psychicExam:id,anamnese_id,appearance,behavior,sense_perception,thought_process,thought_content,language,affectivity,mood,disease_awareness',
                    'anamnese.attitudeWithTheInterviewer:id,anamnese_id,cooperative,resistant,indifferent',
                    'anamnese.guidance:id,anamnese_id,self_identification,body,temporal,spatial,pathology_oriented',
                    'anamnese.notes:id,anamnese_id',
                    'anamnese.diagnosticHypothesis:id,anamnese_id,content',
                    'anamnese.mood:id,anamnese_id,normal,elated,low_mood,sudde_change_mood',
                    'anamnese.awarenessOfCurrentIllness:id,anamnese_id,yes,partially,no',
                ])
                ->select(
                    'id',
                    'full_name',
                    'cpf',
                    'email',
                    'phone',
                    'date_of_birth',
                    'gender',
                    'employee_id'
                )
                ->findOrFail($patient_id);

            $client = $this->currentProfessionalData();

            $tenant = Tenant::find(session('tenant_id'));
            $psychologistLogo = null;

            if ($tenant && $tenant->logo) {
                $imagePath = storage_path('app/public/' . $tenant->logo);
                if (file_exists($imagePath) && is_readable($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $psychologistLogo = 'data:image/' . $imageType . ';base64,' . $imageData;
                }
            }

            $pdf = Pdf::loadView('tenant.pdfs.prontuario', compact('patient', 'psychologistLogo', 'client'));
            $this->auditRecordDownload($patient, $request, [
                'record_version' => 'prontuario_pdf',
                'route' => 'tenant.medicalRecord.pdf.prontuario',
            ]);

            return $pdf->download('prontuario-paciente-' . $patient['id'] . '.pdf');
        } catch (\Throwable $th) {
            throw new Exception($th);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // até 10MB
        ]);

        $result = $this->PatientService->upload($request->file('file'));

        if ($result) {
            return response()->json([
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar paciente'
            ], 500);
        }
    }

    private function patientQueryForCurrentUser()
    {
        $query = Patient::query();

        if (session('is_admin')) {
            return $query;
        }

        $employeeId = Auth::guard('employee')->id();

        if (!$employeeId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('employee_id', $employeeId);
    }

    private function scopeConsultationsToCurrentUser($query): void
    {
        if (session('is_admin')) {
            return;
        }

        $employeeId = Auth::guard('employee')->id();

        if (!$employeeId) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where('employee_id', $employeeId);
    }

    private function currentProfessionalData(): array
    {
        if (session('is_admin')) {
            $client = Auth::guard('client')->user()
                ?: Clients::where('tenant_id', session('tenant_id'))->first();

            return [
                'name' => $client?->name,
                'cpf' => $client?->cpf,
                'phone' => $client?->phone,
                'crp' => $client?->crp,
            ];
        }

        $employee = Auth::guard('employee')->user();

        return [
            'name' => $employee?->name,
            'cpf' => $employee?->cpf,
            'phone' => $employee?->phone,
            'crp' => $employee?->crp,
        ];
    }
}
