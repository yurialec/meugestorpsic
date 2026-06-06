<?php

namespace App\Services\Tenants;

use App\Models\Admin\PaymentMethod;
use App\Repositories\Tenants\ConsultationRepository;
use Auth;
use Carbon\Carbon;

class ConsultationService
{
    protected $ConsultationRepository;

    public function __construct(ConsultationRepository $ConsultationRepository)
    {
        $this->ConsultationRepository = $ConsultationRepository;
    }

    public function all($term)
    {
        return $this->ConsultationRepository->all($term);
    }

    public function store($data)
    {
        $consultationData = [
            'patient_id' => $data['patient_id'],
            'tenant_id' => $data['tenant_id'],
            'client_appointment_id' => $data['client_appointment_id'],
            'scheduled_at' => $data['day'] . ' ' . $data['hour'],
        ];

        if (!session('is_admin') && Auth::guard('employee')->id()) {
            $consultationData['employee_id'] = Auth::guard('employee')->id();
        } elseif (isset($data['employee_id'])) {
            $consultationData['employee_id'] = $data['employee_id'];
        }

        return $this->ConsultationRepository->store($consultationData);
    }

    public function find($id)
    {
        return $this->ConsultationRepository->find($id);
    }

    public function update($id, $data)
    {
        $status = $data['status'] ?? 'open';

        if (isset($data['status']) && $data['status'] === 'canceled') {
            $status = 'canceled';
        }

        if (isset($data['started_at']) && isset($data['ended_at']) && $data['started_at'] && $data['ended_at']) {
            $status = 'done';
        }

        $consultationData = [
            'started_at' => $data['started_at'] ?? null,
            'ended_at' => $data['ended_at'] ?? null,
            'objectives' => $data['objectives'] ?? null,
            'content_worked' => $data['content_worked'] ?? null,
            'clinical_observations' => $data['clinical_observations'] ?? null,
            'interventions' => $data['interventions'] ?? null,
            'planning' => $data['planning'] ?? null,
            'homework' => $data['homework'] ?? null,
            'insights' => $data['insights'] ?? null,
            'emotional_state' => $data['emotional_state'] ?? null,
            'engagement_level' => $data['engagement_level'] ?? null,
            'status' => $status,
            'location' => $data['location'] ?? null,
        ];

        if ($status === 'canceled' && empty($data['finance'])) {
            return $this->ConsultationRepository->update($id, $consultationData, null, null); // Cancelamento atualiza consulta/agendamento sem criar financeiro.
        }

        $financeData = [
            'amount' => $data['finance']['payment_amount'],
            'payment_method_id' => $data['finance']['payment_method_id'],
            'status' => $data['finance']['payment_status'],
            'discount' => $data['finance']['discount'],
        ];

        if ($financeData['status'] == 'free' || $financeData['status'] == 'pending') {
            $financeData['payment_method_id'] = PaymentMethod::freeOrPending($financeData['status']);
        }

        $financeInstallments = null;

        if (
            $data['finance']['payment_method_id'] === 3
            && $data['finance']['credit_type'] == 'parcelado'
        ) {
            $financeInstallments = [
                'installments' => $data['finance']['installments'],
            ];
        }

        if ($financeData['status'] == 'paid') {
            $financeData['paid_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $this->ConsultationRepository->update($id, $consultationData, $financeData, $financeInstallments);
    }
}
