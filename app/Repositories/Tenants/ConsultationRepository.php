<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\ConsultationRepositoryInterface;
use App\Models\Admin\PaymentMethod;
use App\Models\Tenants\Appointment;
use App\Models\Tenants\Consultation;
use App\Models\Tenants\TenantFinancialInstallment;
use App\Models\Tenants\TenantFinancialTransaction;
use Auth;
use DB;
use Log;

class ConsultationRepository implements ConsultationRepositoryInterface
{
    protected $consultation;
    protected $appointment;
    protected $financialTransaction;
    protected $installment;

    public function __construct(
        Consultation $consultation,
        Appointment $appointment,
        TenantFinancialTransaction $financialTransaction,
        TenantFinancialInstallment $installment,
    ) {
        $this->consultation = $consultation;
        $this->appointment = $appointment;
        $this->financialTransaction = $financialTransaction;
        $this->installment = $installment;
    }

    public function all($term)
    {
        try {
            $consultation = $this->consultation
                ->select([
                    'id',
                    'tenant_id',
                    'patient_id',
                    'client_appointment_id',
                    'scheduled_at',
                    'started_at',
                    'ended_at',
                    'status',
                    'location',
                    'created_at',
                    'updated_at',
                ])
                ->with(['patient'])
                ->when($term, function ($query) use ($term) {
                    return $query->where(function ($q) use ($term) {
                        $q->where('status', 'like', '%' . $term . '%')
                            ->orWhereDate('scheduled_at', 'like', '%' . $term . '%')
                            ->orWhereHas('patient', function ($patientQuery) use ($term) {
                                $patientQuery->where('full_name', 'like', '%' . $term . '%')
                                    ->orWhere('cpf', 'like', '%' . $term . '%');
                            });
                    });
                });
            $this->scopeToCurrentEmployee($consultation);

            return $consultation->paginate(10);
        } catch (\Exception $err) {
            Log::error('Erro ao listar consultas', [$err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        try {
            $consultation = $this->consultation->with('patient')->whereKey($id);
            $this->scopeToCurrentEmployee($consultation);

            return $consultation->first();
        } catch (\Exception $err) {
            Log::error('Erro ao listar consultas', [$err->getMessage()]);
            return false;
        }
    }

    public function store(array $data)
    {        
        DB::beginTransaction();
        try {
            $existingConsultation = $this->consultation
                ->where('patient_id', $data['patient_id'])
                ->where('scheduled_at', $data['scheduled_at'])
                ->first();

            if ($existingConsultation) {
                return $existingConsultation;
            }

            $consultation = $this->consultation->create($data);

            DB::commit();
            return $consultation;
        } catch (\Exception $err) {
            DB::rollback();
            Log::error('Erro ao cadastrar consulta', [$err->getMessage()]);
            return false;
        }
    }

    public function update($id, $consultationData, $financeData, $financeInstallments)
    {
        try {
            DB::beginTransaction();
            $consultation = $this->consultation->whereKey($id);
            $this->scopeToCurrentEmployee($consultation);
            $consultation = $consultation->first();

            if (!$consultation) {
                DB::rollBack();
                return false;
            }

            $consultation->update($consultationData);

            $appointment = $this->appointment
                ->find($consultation->client_appointment_id);

            if ($appointment) {
                $appointment->status = $this->mapConsultationStatusToAppointmentStatus($consultation->status); // Converte open/done/canceled para o enum Open/Done/Canceled do agendamento.
                $appointment->save();
            }

            if (!$financeData) {
                DB::commit();
                return $consultation; // Cancelamentos nao precisam gerar lancamento financeiro.
            }

            $financeData['tenant_id'] = $consultation->tenant_id;
            $financeData['patient_id'] = $consultation->patient_id;
            $financeData['consultation_id'] = $consultation->id;
            $financeData['consultation_id'] = $consultation->id;

            $finance = $this->financialTransaction->create($financeData);

            if ($financeInstallments) {

                $baseAmount = round($finance->amount / $financeInstallments['installments'], 2);
                $totalBase = $baseAmount * ($financeInstallments['installments'] - 1);
                $lastAmount = round($finance->amount - $totalBase, 2);

                for ($i = 1; $i <= $financeInstallments['installments']; $i++) {
                    $this->installment->create([
                        'financial_transaction_id' => $finance->id,
                        'num' => $i,
                        'amount' => ($i === $financeInstallments['installments']) ? $lastAmount : $baseAmount,
                    ]);
                }
            }

            DB::commit();
            return $consultation;
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error('Erro ao atualizar consulta', [$err->getMessage()]);
            return false;
        }
    }

    public function delete($id)
    {
        return Consultation::destroy($id);
    }

    private function scopeToCurrentEmployee($query): void
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

    private function mapConsultationStatusToAppointmentStatus(string $status): string
    {
        return match ($status) {
            'done' => Appointment::STATUS_DONE,
            'canceled' => Appointment::STATUS_CANCELED,
            default => Appointment::STATUS_OPEN,
        };
    }
}
