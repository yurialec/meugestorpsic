<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\AppointmentRepositoryInterface;
use App\Mail\AppointmentScheduled;
use App\Models\Admin\Clients;
use App\Models\Admin\PaymentMethod;
use App\Models\Tenants\Appointment;
use App\Models\Tenants\Patient;
use App\Models\Tenants\TenantFinancialInstallment;
use App\Models\Tenants\TenantFinancialTransaction;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Log;
use Mail;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    protected $patients;
    protected $appointment;
    protected $financialTransaction;
    protected $installment;

    public function __construct(
        Appointment $appointment,
        Patient $patients,
        TenantFinancialTransaction $financialTransaction,
        TenantFinancialInstallment $installment
    ) {
        $this->appointment = $appointment;
        $this->patients = $patients;
        $this->financialTransaction = $financialTransaction;
        $this->installment = $installment;
    }

    public function list()
    {
        try {
            return $this->appointment->get();
        } catch (Exception $err) {
            Log::error('Error', ['Erro' => $err->getMessage()]);
        }
    }

    public function find($id)
    {
        return Appointment::find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {

            $appointment = new Appointment();
            $appointment->tenant_id = $data['tenant_id'];
            $appointment->patient_id = $data['patient_id'];
            $appointment->day = $data['day'];
            $appointment->hour = $data['hour'];
            $appointment->status = $data['status'] ?? Appointment::STATUS_OPEN;

            if (isset($data['employee_id'])) {
                $appointment->employee_id = $data['employee_id'];
            }

            $appointment->save();

            if (!empty($data['finance'])) {
                $this->createFinancialTransaction($appointment, $data['finance']); // Registra no financeiro no mesmo commit do agendamento.
            }

            DB::commit();

            $patient = Patient::find($data['patient_id']);
            if ($patient && !empty($patient->email)) {
                try {
                    Mail::to($patient->email)->send(new AppointmentScheduled($patient, $appointment));
                } catch (Exception $mailErr) {
                    Log::error('Erro ao enviar email de agendamento', [
                        'appointment_id' => $appointment->id,
                        'patient_id' => $patient->id,
                        'Erro' => $mailErr->getMessage(),
                    ]);
                }
            }

            return $appointment;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('Erro ao agendar paciente', ['Erro' => $err->getMessage()]);
            return false;
        }
    }

    public function update($id, array $data)
    {
        $model = Appointment::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        return Appointment::destroy($id);
    }

    public function getPatients()
    {
        try {
            return $this->patients->get();
        } catch (Exception $err) {
            Log::error('Error', ['Erro' => $err->getMessage()]);
        }
    }

    public function removePatient($data)
    {
        try {
            $appointment = !empty($data['appointment_id'])
                ? $this->appointment->whereKey($data['appointment_id'])->first()
                : $this->appointment
                    ->whereDate('day', $data['day'])
                    ->where('hour', Carbon::parse($data['hour'])->format('H:i:s')) // Normaliza HH:mm para o formato time do banco.
                    ->where('patient_id', $data['patient_id'])
                    ->first();

            if ($appointment) {
                $appointment->status = Appointment::STATUS_CANCELED; // Cancela antes do soft delete para manter historico coerente.
                $appointment->save();
                $appointment->delete();
            }

            return true;
        } catch (Exception $err) {
            Log::error('Erro ao remover agendamento', ['exception' => $err->getMessage()]);
            return false;
        }
    }

    private function createFinancialTransaction(Appointment $appointment, array $finance): void
    {
        $status = $finance['payment_status'] ?? TenantFinancialTransaction::STATUS_PENDING;
        $paymentMethodId = $finance['payment_method_id'] ?? null;

        if (in_array($status, [TenantFinancialTransaction::STATUS_FREE, TenantFinancialTransaction::STATUS_PENDING], true)) {
            $paymentMethodId = PaymentMethod::freeOrPending($status); // Converte "pending/free" para o ID real da tabela payment_method.
        }

        $transaction = $this->financialTransaction->create([
            'tenant_id' => $appointment->tenant_id,
            'patient_id' => $appointment->patient_id,
            'amount' => $finance['payment_amount'] ?? 0,
            'payment_method_id' => $paymentMethodId,
            'status' => $status,
            'paid_at' => $status === TenantFinancialTransaction::STATUS_PAID ? Carbon::now()->format('Y-m-d H:i:s') : null,
            'description' => "Agendamento confirmado em {$appointment->day} às {$appointment->hour}",
            'discount' => $finance['discount'] ?? 0,
        ]);

        if (($finance['payment_method_id'] ?? null) == 3 && ($finance['credit_type'] ?? null) === 'parcelado') {
            $installments = (int) ($finance['installments'] ?? 0);
            if ($installments > 1) {
                $baseAmount = round($transaction->amount / $installments, 2);
                $totalBase = $baseAmount * ($installments - 1);
                $lastAmount = round($transaction->amount - $totalBase, 2);

                for ($i = 1; $i <= $installments; $i++) {
                    $this->installment->create([
                        'financial_transaction_id' => $transaction->id,
                        'num' => $i,
                        'amount' => ($i === $installments) ? $lastAmount : $baseAmount,
                    ]);
                }
            }
        }
    }

    public function findForConsultation($idPatient, $day, $hour)
    {
        try {

            $appointment = $this->appointment
                ->whereDate('day', $day)
                ->where('hour', Carbon::parse($hour)->format('H:i:s')) // Normaliza HH:mm vindo da rota antes de consultar.
                ->where('patient_id', $idPatient)
                ->first();

            if (!$appointment) {
                return null;
            }

            if (!session('is_admin')) {
                $employee_id = Auth::guard('employee')->id();
                if ($appointment->employee_id !== $employee_id) {

                    return 'Você não tem permissão para atender esse paciente';
                }
            }

            return $appointment;
        } catch (Exception $err) {
            Log::error('Erro ao localizar consulta', ['exception' => $err->getMessage()]);
            return false;
        }
    }
}
