<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\PatientRepositoryInterface;
use App\Models\Tenants\Patient;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Log;

class PatientRepository implements PatientRepositoryInterface
{
    protected $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function all($term)
    {
        try {
            $patient = $this->patient;

            if (!session('is_admin')) {
                $patient = $patient->where('employee_id', Auth::guard('employee')->id());
            }

            $patients = $patient->when($term, function ($query) use ($term) {
                return $query->where(function ($q) use ($term) {
                    $q->where('full_name', 'like', '%' . $term . '%')
                        ->orWhere('cpf', 'like', '%' . $term . '%');
                });
            })->paginate(10);

            return $patients;
        } catch (Exception $err) {
            Log::error('Erro ao listar pacientes', [$err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        return Patient::find($id);
    }

    public function create(array $data): ?Patient
    {
        try {
            if (!empty($data['cpf']) && $this->cpfExistsForTenant($data['cpf'], $data['tenant_id'] ?? session('tenant_id'))) {
                Log::warning('Tentativa de cadastrar paciente com CPF duplicado no tenant', [
                    'cpf' => $data['cpf'],
                    'tenant_id' => $data['tenant_id'] ?? session('tenant_id'),
                ]);

                return null;
            }

            return $this->patient->create($data);
        } catch (Exception $e) {
            Log::error('Erro ao criar paciente', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function update($id, array $data)
    {
        try {
            $patient = $this->patient->find($id);
            $patient->fill($data);
            return $patient->save();
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function disable($id)
    {
        try {
            $patient = $this->patient->find($id);
            if (!$patient) {
                return null;
            }

            $patient->deleted_at ? $patient->deleted_at = null : $patient->deleted_at = Carbon::now();
            return $patient->save();
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getPatientById($id)
    {
        try {
            return $this->patient->find($id);
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function upload($data)
    {
        $results = [
            'created' => [],
            'skipped' => [],
            'errors' => []
        ];

        foreach ($data as $index => $patientData) {
            try {
                if (!empty($patientData['cpf'])) {
                    $tenantId = $patientData['tenant_id'] ?? session('tenant_id');

                    if ($this->cpfExistsForTenant($patientData['cpf'], $tenantId)) {
                        $results['skipped'][] = [
                            'row' => $index + 1,
                            'reason' => 'CPF já cadastrado',
                            'cpf' => $patientData['cpf']
                        ];
                        continue;
                    }
                }

                if (!empty($patientData['email'])) {
                    $existingByEmail = $this->patient->where('email', $patientData['email'])->first();
                    if ($existingByEmail) {
                        $results['skipped'][] = [
                            'row' => $index + 1,
                            'reason' => 'E-mail já cadastrado',
                            'email' => $patientData['email']
                        ];
                        continue;
                    }
                }

                $created = $this->patient->create($patientData);
                $results['created'][] = $created->id;

            } catch (Exception $e) {
                Log::error('Erro ao importar paciente na linha ' . ($index + 1), [
                    'data' => $patientData,
                    'error' => $e->getMessage()
                ]);

                $results['errors'][] = [
                    'row' => $index + 1,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    private function cpfExistsForTenant(string $cpf, $tenantId): bool
    {
        return $this->patient
            ->where('cpf', $cpf)
            ->where('tenant_id', $tenantId)
            ->exists();
    }
}
